<?php

namespace Middleware;

use Controllers\Interface\ControllerInterface;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InvalidSessionException;
use Middleware\Interface\MiddlewareInterface;
use Render\interface\HTTPRenderer;
use Render\JSONRenderer;
use Helpers\SessionManager;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(ControllerInterface $controller): HTTPRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return new JSONRenderer(200, []);
        }
        if (is_null(SessionManager::get('user_id')) || is_null(SessionManager::get('user_name'))) {
            throw new InvalidSessionException('Request has no session.');
        }
        $usersDAOImpl = new UsersDAOImpl();
        // TODO インメモリデータベース(Redis)で高速化する
        $userSelectedById = $usersDAOImpl->getById(SessionManager::get('user_id'));
        $userSelectedByName = $usersDAOImpl->getByName(SessionManager::get('user_name'));
        if (is_null($userSelectedById) || is_null($userSelectedByName)) {
            throw new InvalidSessionException('Given session has no related user.');
        }
        if ($userSelectedById->getName() !== $userSelectedByName->getName()) {
            throw new InvalidSessionException('Given session has invalid user data.');
        }
        return $controller->handleRequest();
    }
}
