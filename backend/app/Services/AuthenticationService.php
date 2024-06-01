<?php

namespace Services;

use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InvalidRequestParameterException;
use Exceptions\InvalidSessionException;
use Helpers\RedisManager;
use Helpers\SessionManager;
use Models\User;

class AuthenticationService
{
    private UsersDAOImpl $usersDAOImpl;

    public function __construct(UsersDAOImpl $usersDAOImpl)
    {
        $this->usersDAOImpl = $usersDAOImpl;
    }

    public function login(string $userName, string $password): User
    {
        $userInTable = $this->usersDAOImpl->getByName($userName);
        if (is_null($userInTable)) {
            throw new InvalidRequestParameterException("Specified 'username' does not exist.");
        }
        if (!$this->verifyPassword($password, $userInTable->getPasswordHash())) {
            throw new InvalidRequestParameterException("Invalid password.");
        }
        $userInTable->setLastLogin(date('Y-m-d H:i:s'));
        $this->usersDAOImpl->update($userInTable);
        return $userInTable;
    }

    public function setLoginDataInSession(int $userId, string $userName): void
    {
        $this->setSessionData($userId, $userName);
        $this->setRedisData($userId, $userName);
    }

    public function checkLoginDataInSession(): void
    {
        $this->checkSessionData();
        $this->checkRedisData(
            userId: SessionManager::get('user_id'),
            userName: SessionManager::get('user_name')
        );
    }

    public function logout(string $userId): void
    {
        $this->unsetSessionData();
        $this->unsetRedisData($userId);
    }

    private function verifyPassword(string $password, string $passwordHash): bool
    {
        return password_verify($password, $passwordHash);
    }

    private function setSessionData(int $userId, string $userName): void
    {
        SessionManager::set('user_id', $userId);
        SessionManager::set('user_name', $userName);
    }

    private function checkSessionData(): void
    {
        $userId = SessionManager::get('user_id');
        $userName = SessionManager::get('user_name');
        if (is_null($userId) || is_null($userName)) {
            throw new InvalidSessionException('Request has no session.');
        }
    }

    private function unsetSessionData(): void
    {
        SessionManager::destroySession();
    }

    private function setRedisData(int $userId, string $userName): void
    {
        $redisClient = RedisManager::getInstance();
        $redisClient->set($userId, $userName);
    }

    private function checkRedisData(int $userId, string $userName): void
    {
        $redisClient = RedisManager::getInstance();
        $userNameInSession = $redisClient->get($userId);
        if (is_null($userNameInSession)) {
            throw new InvalidSessionException('Given session has no related user.');
        }
        if ($userName !== $userNameInSession) {
            throw new InvalidSessionException('Given session has invalid user data.');
        }
    }

    private function unsetRedisData(int $userId): void
    {
        $redisClient = RedisManager::getInstance();
        $redisClient->del($userId);
    }
}
