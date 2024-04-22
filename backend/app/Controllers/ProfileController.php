<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;
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
        $userId = SessionManager::get('user_id');
        $profile = $this->profileService->getUserInfo($userId);
        return new JSONRenderer(200, ['profile' => $profile]);
    }

    private function updateProfile(PostProfileRequest $request): JSONRenderer
    {
        $newUser = $this->profileService->updateUser($request, SessionManager::get('user_id'));
        $verificationUrl = $this->signupService->publishUserVerificationURL($newUser);
        if (!is_null($newUser->getEmailVerifiedAt())) {
            $this->profileService->sendVerificationEmail($newUser, $verificationUrl);
        }
        SessionManager::set('user_id', $newUser->getId());
        SessionManager::set('user_name', $newUser->getName());
        return new JSONRenderer(200, []);
    }

    private function deleteProfile(): JSONRenderer
    {
        $userId = SessionManager::get('user_id');
        $this->profileService->deleteUser($userId);
        return new JSONRenderer(200, []);
    }
}
