<?php

namespace Middleware;

use Controllers\Interface\ControllerInterface;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InvalidSessionException;
use Helpers\RedisManager;
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
        $userId = SessionManager::get('user_id');
        $userName = SessionManager::get('user_name');

        $redisClient = RedisManager::getInstance();
        $userNameInSession = $redisClient->get($userId);
        if (is_null($userNameInSession)) {
            throw new InvalidSessionException('Given session has no related user.');
        }
        if ($userName !== $userNameInSession) {
            throw new InvalidSessionException('Given session has invalid user data.');
        }

        return $controller->handleRequest();
    }
}
