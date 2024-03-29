<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Exceptions\InvalidRequestParameterException;
use Http\Request\SignupRequest;
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new InvalidRequestMethodException("SignUp request must be 'POST'.");
        }
        $request = new SignupRequest($_POST, $_FILES);
        return $this->signup($request);
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
}
