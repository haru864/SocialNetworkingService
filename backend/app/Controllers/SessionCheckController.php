<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;

class SessionCheckController implements ControllerInterface
{
    public function __construct()
    {
    }

    public function handleRequest(): JSONRenderer
    {
        return new JSONRenderer(200, []);
    }
}
