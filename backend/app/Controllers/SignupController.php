<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;
use Http\Request\SignupRequest;
use Http\Request\ValidateEmailRequest;
use Services\SignupService;

class SignupController implements ControllerInterface
{
    private SignupService $signupService;

    public function __construct(SignupService $signupService)
    {
        $this->signupService = $signupService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!preg_match('/multipart\/form-data/', $_SERVER['CONTENT_TYPE'])) {
                throw new InvalidRequestMethodException("SignUp request must be 'multipart/form-data'.");
            }
            $request = new SignupRequest($_POST, $_FILES);
            return $this->signup($request);
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $request = new ValidateEmailRequest($_GET);
            return $this->validateEmail($request);
        } else {
            throw new InvalidRequestMethodException("SignUp request must be 'POST', email verification request must be 'GET'.");
        }
    }

    private function signup(SignupRequest $request): JSONRenderer
    {
        $pendingUser = $this->signupService->createPendingUser($request);
        $this->signupService->sendVerificationEmail($pendingUser);
        return new JSONRenderer(200, []);
    }

    private function validateEmail(ValidateEmailRequest $request): JSONRenderer
    {
        $this->signupService->validateEmail($request->getId());
        $user = $this->signupService->createUserByPending($request->getId());
        SessionManager::set('user_id', $user->getId());
        SessionManager::set('user_name', $user->getName());
        return new JSONRenderer(200, []);
    }
}
