<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;

class LogoutController implements ControllerInterface
{
    public function __construct()
    {
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
        SessionManager::destroySession();
        return new JSONRenderer(200, []);
    }
}
