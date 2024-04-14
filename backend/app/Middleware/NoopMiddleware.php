<?php

namespace Middleware;

use Controllers\Interface\ControllerInterface;
use Middleware\Interface\MiddlewareInterface;
use Render\interface\HTTPRenderer;
use Render\JSONRenderer;
use Settings\Settings;

class NoopMiddleware implements MiddlewareInterface
{
    public function handle(ControllerInterface $controller): HTTPRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return new JSONRenderer(200, []);
        }
        return $controller->handleRequest();
    }
}
