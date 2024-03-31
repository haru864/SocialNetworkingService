<?php

namespace Services;

use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InvalidRequestParameterException;
use Http\Request\LoginRequest;
use Models\User;

class LoginService
{
    private UsersDAOImpl $usersDAOImpl;

    public function __construct(usersDAOImpl $usersDAOImpl)
    {
        $this->usersDAOImpl = $usersDAOImpl;
    }

    public function login(LoginRequest $loginRequest): User
    {
        $username = $loginRequest->getUsername();
        $userInTable = $this->usersDAOImpl->getByName($username);
        if (is_null($userInTable)) {
            throw new InvalidRequestParameterException("Specified 'username' does not exist.");
        }
        $password = $loginRequest->getPassword();
        $passwordHash = $userInTable->getPasswordHash();
        $isValidPassword = password_verify($password, $passwordHash);
        if (!$isValidPassword) {
            throw new InvalidRequestParameterException("Invalid password.");
        }
        $userInTable->setLastLogin(date('Y-m-d H:i:s'));
        $this->usersDAOImpl->update($userInTable);
        return $userInTable;
    }
}
