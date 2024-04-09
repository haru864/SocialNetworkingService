<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Http\Request\GetProfileRequest;
use Http\Request\PostProfileRequest;
use Services\ProfileService;
use Services\SignupService;

class ProfileController implements ControllerInterface
{
    private ProfileService $profileService;
    private SignupService $signupService;

    public function __construct(ProfileService $profileService, SignupService $signupService)
    {
        $this->profileService = $profileService;
        $this->signupService = $signupService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!preg_match('/multipart\/form-data/', $_SERVER['CONTENT_TYPE'])) {
                throw new InvalidRequestMethodException("SignUp request must be 'multipart/form-data'.");
            }
            $request = new PostProfileRequest($_POST, $_FILES);
            if ($request->getAction() === 'edit') {
                return $this->updateProfile($request);
            } else if ($request->getAction() === 'delete') {
                return $this->deleteProfile();
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->getProfile(new GetProfileRequest());
        } else {
            throw new InvalidRequestMethodException("SignUp request must be 'POST', email verification request must be 'GET'.");
        }
    }

    private function getProfile(): JSONRenderer
    {
        $userId = $_SESSION['user_id'];
        $profile = $this->profileService->getUserInfo($userId);
        return new JSONRenderer(200, ['profile' => $profile]);
    }

    private function updateProfile(PostProfileRequest $request): JSONRenderer
    {
        $newUser = $this->profileService->updateUser($request, $_SESSION['user_id']);
        if (is_null($newUser->getEmailVerifiedAt())) {
            $this->signupService->sendVerificationEmail($newUser);
        } else {
            session_start();
            $_SESSION['user_id'] = $newUser->getId();
            $_SESSION['user_name'] = $newUser->getName();
        }
        return new JSONRenderer(200, []);
    }

    private function deleteProfile(): JSONRenderer
    {
        $userId = $_SESSION['user_id'];
        $this->profileService->deleteUser($userId);
        return new JSONRenderer(200, []);
    }
}
