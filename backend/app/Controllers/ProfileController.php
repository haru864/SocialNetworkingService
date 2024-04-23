<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;
use Http\Request\GetProfileRequest;
use Http\Request\PostProfileRequest;
use Http\Request\ValidateEmailRequest;
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
                return $this->createPendingData($request);
            } else if ($request->getAction() === 'delete') {
                return $this->deleteProfile();
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $isEmailValidation = strpos($_SERVER['REQUEST_URI'], 'validate_email') !== false;
            if ($isEmailValidation) {
                return $this->validateEmail(new ValidateEmailRequest($_GET));
            } else {
                return $this->getProfile(new GetProfileRequest());
            }
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

    private function createPendingData(PostProfileRequest $request): JSONRenderer
    {
        $pendingUser = $this->profileService->createPendingUser($request, SessionManager::get('user_id'));
        $url = $this->signupService->publishUserVerificationURL($pendingUser);
        $this->profileService->sendVerificationEmail($pendingUser, $url);
        return new JSONRenderer(200, []);
    }

    private function validateEmail(ValidateEmailRequest $request): JSONRenderer
    {
        $this->signupService->validateEmail($request->getId());
        $user = $this->profileService->updateUserByPending($request->getId());
        SessionManager::set('user_id', $user->getId());
        SessionManager::set('user_name', $user->getName());
        return new JSONRenderer(200, []);
    }

    private function deleteProfile(): JSONRenderer
    {
        $userId = SessionManager::get('user_id');
        $this->profileService->deleteUser($userId);
        return new JSONRenderer(200, []);
    }
}
