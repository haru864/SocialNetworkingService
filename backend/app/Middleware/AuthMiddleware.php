<?php

namespace Middleware;

use Controllers\Interface\ControllerInterface;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InvalidSessionException;
use Render\interface\HTTPRenderer;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(ControllerInterface $controller): HTTPRenderer
    {
        session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
            return new InvalidSessionException('Request has no session.');
        }
        $usersDAOImpl = new UsersDAOImpl();
        $userSelectedById = $usersDAOImpl->getById($_SESSION['user_id']);
        $userSelectedByName = $usersDAOImpl->getById($_SESSION['user_name']);
        if (is_null($userSelectedById) || is_null($userSelectedByName)) {
            return new InvalidSessionException('Given session has no related user.');
        }
        if ($userSelectedById->getName() !== $userSelectedByName->getName()) {
            return new InvalidSessionException('Given session has invalid user data.');
        }
        return $controller->handleRequest();
    }
}
