<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;
use Http\Request\GetProfileRequest;
use Http\Request\UpdateProfileRequest;
use Http\Request\ValidateEmailRequest;
use Services\AuthenticationService;
use Services\ProfileService;
use Services\SignupService;

class ProfileController implements ControllerInterface
{
    private ProfileService $profileService;
    private SignupService $signupService;
    private AuthenticationService $authenticationService;

    public function __construct(
        ProfileService $profileService,
        SignupService $signupService,
        AuthenticationService $authenticationService
    ) {
        $this->profileService = $profileService;
        $this->signupService = $signupService;
        $this->authenticationService = $authenticationService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!preg_match('/multipart\/form-data/', $_SERVER['CONTENT_TYPE'])) {
                throw new InvalidRequestMethodException("SignUp request must be 'multipart/form-data'.");
            }
            $request = new UpdateProfileRequest($_POST, $_FILES);
            return $this->createPendingData($request);
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $isEmailValidation = strpos($_SERVER['REQUEST_URI'], 'validate_email') !== false;
            if ($isEmailValidation) {
                return $this->validateEmail(new ValidateEmailRequest($_GET));
            }
            $request = new GetProfileRequest($_GET);
            if (is_null($request->getUserId())) {
                return $this->getProfileBySession();
            } else {
                return $this->getProfileByUserId($request);
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            return $this->deleteProfile();
        } else {
            throw new InvalidRequestMethodException("Invalid Request Method.");
        }
    }

    private function getProfileBySession(): JSONRenderer
    {
        $userId = SessionManager::get('user_id');
        $profile = $this->profileService->getUserInfo($userId, false);
        return new JSONRenderer(200, ['profile' => $profile]);
    }

    private function getProfileByUserId(GetProfileRequest $request): JSONRenderer
    {
        $userId = $request->getUserId();
        $profile = $this->profileService->getUserInfo($userId, true);
        return new JSONRenderer(200, ['profile' => $profile]);
    }

    private function createPendingData(UpdateProfileRequest $request): JSONRenderer
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
        $this->authenticationService->setLoginDataInSession(
            userId: $user->getId(),
            userName: $user->getName()
        );
        return new JSONRenderer(200, []);
    }

    private function deleteProfile(): JSONRenderer
    {
        $userId = SessionManager::get('user_id');
        $this->profileService->deleteUser($userId);
        $this->authenticationService->logout($userId);
        return new JSONRenderer(200, []);
    }
}
