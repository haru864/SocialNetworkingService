<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Helpers\SessionManager;
use Render\JSONRenderer;

class SessionCheckController implements ControllerInterface
{
    public function __construct()
    {
    }

    public function handleRequest(): JSONRenderer
    {
        $userId = SessionManager::get('user_id');
        return new JSONRenderer(200, ['user_id' => $userId]);
    }
}
