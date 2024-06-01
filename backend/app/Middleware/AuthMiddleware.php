<?php

namespace Middleware;

use Controllers\Interface\ControllerInterface;
use Middleware\Interface\MiddlewareInterface;
use Render\interface\HTTPRenderer;
use Render\JSONRenderer;
use Services\AuthenticationService;

class AuthMiddleware implements MiddlewareInterface
{
    private AuthenticationService $authenticationService;

    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function handle(ControllerInterface $controller): HTTPRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return new JSONRenderer(200, []);
        }
        $this->authenticationService->checkLoginDataInSession();
        return $controller->handleRequest();
    }
}
