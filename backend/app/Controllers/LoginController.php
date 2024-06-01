<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Http\Request\LoginRequest;
use Services\AuthenticationService;

class LoginController implements ControllerInterface
{
    private AuthenticationService $authenticationService;

    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new InvalidRequestMethodException("login request must be 'POST'.");
        }
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            throw new InvalidRequestMethodException("login request must be 'application/json'.");
        }
        $jsonData = file_get_contents('php://input');
        $reqParamMap = json_decode($jsonData, true);
        $loginRequest = new LoginRequest($reqParamMap);
        return $this->login($loginRequest);
    }

    private function login(LoginRequest $loginRequest): JSONRenderer
    {
        $user = $this->authenticationService->login($loginRequest->getUsername(), $loginRequest->getPassword());
        $this->authenticationService->setLoginDataInSession($user->getId(), $user->getName());
        return new JSONRenderer(200, []);
    }
}
