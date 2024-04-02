<?php

namespace Middleware;

use Controllers\Interface\ControllerInterface;
use Middleware\Interface\MiddlewareInterface;
use Render\interface\HTTPRenderer;

class NoopMiddleware implements MiddlewareInterface
{
    public function handle(ControllerInterface $controller): HTTPRenderer
    {
        return $controller->handleRequest();
    }
}
