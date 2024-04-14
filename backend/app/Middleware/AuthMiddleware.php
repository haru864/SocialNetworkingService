<?php

namespace Middleware;

use Controllers\Interface\ControllerInterface;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InvalidSessionException;
use Middleware\Interface\MiddlewareInterface;
use Render\interface\HTTPRenderer;
use Render\JSONRenderer;
use Settings\Settings;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(ControllerInterface $controller): HTTPRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return new JSONRenderer(200, []);
        }
        session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
            throw new InvalidSessionException('Request has no session.');
        }
        $usersDAOImpl = new UsersDAOImpl();
        // TODO インメモリデータベース(Redis)で高速化する
        $userSelectedById = $usersDAOImpl->getById($_SESSION['user_id']);
        $userSelectedByName = $usersDAOImpl->getByName($_SESSION['user_name']);
        if (is_null($userSelectedById) || is_null($userSelectedByName)) {
            throw new InvalidSessionException('Given session has no related user.');
        }
        if ($userSelectedById->getName() !== $userSelectedByName->getName()) {
            throw new InvalidSessionException('Given session has invalid user data.');
        }
        return $controller->handleRequest();
    }
}
