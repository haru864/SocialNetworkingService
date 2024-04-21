<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;
use Http\Request\LoginRequest;
use Services\LoginService;

class LoginController implements ControllerInterface
{
    private LoginService $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
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
        $user = $this->loginService->login($loginRequest);
        SessionManager::set('user_id', $user->getId());
        SessionManager::set('user_name', $user->getName());
        return new JSONRenderer(200, []);
    }
}
