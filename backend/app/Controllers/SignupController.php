<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Exceptions\InvalidRequestParameterException;
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
        try {
            $user = $this->signupService->createUser($request);
            $this->signupService->sendVerificationEmail($user);
            return new JSONRenderer(200, []);
        } catch (InvalidRequestParameterException $e) {
            return new JSONRenderer(400, ["error_message" => $e->displayErrorMessage()]);
        }
    }

    private function validateEmail(ValidateEmailRequest $request): JSONRenderer
    {
        try {
            $user = $this->signupService->validateEmail($request->getId());
            session_start();
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_name'] = $user->getName();
            return new JSONRenderer(200, []);
        } catch (InvalidRequestParameterException $e) {
            return new JSONRenderer(400, ["error_message" => $e->displayErrorMessage()]);
        }
    }
}