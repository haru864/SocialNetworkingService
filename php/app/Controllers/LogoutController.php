<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;
use Services\AuthenticationService;

class LogoutController implements ControllerInterface
{
    private AuthenticationService $authenticationService;

    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new InvalidRequestMethodException("logout request must be 'POST'.");
        }
        return $this->logout();
    }

    private function logout(): JSONRenderer
    {
        $userId = SessionManager::get('user_id');
        $this->authenticationService->logout($userId);
        return new JSONRenderer(200, []);
    }
}
