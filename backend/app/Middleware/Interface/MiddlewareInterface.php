<?php

namespace Middleware\Interface;

use Controllers\Interface\ControllerInterface;
use Render\interface\HTTPRenderer;

interface MiddlewareInterface
{
    public function handle(ControllerInterface $controller): HTTPRenderer;
}
