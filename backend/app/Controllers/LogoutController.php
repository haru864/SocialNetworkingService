<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;

class LogoutController implements ControllerInterface
{
    public function __construct()
    {
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            throw new InvalidRequestMethodException("logout request must be 'GET'.");
        }
        return $this->logout();
    }

    private function logout(): JSONRenderer
    {
        session_start();
        unset($_SESSION["user_id"]);
        unset($_SESSION["user_name"]);
        return new JSONRenderer(200, []);
    }
}
