<?php

namespace Services;

use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InvalidRequestParameterException;
use Http\Request\LoginRequest;

class LoginService
{
    private UsersDAOImpl $usersDAOImpl;

    public function __construct(usersDAOImpl $usersDAOImpl)
    {
        $this->usersDAOImpl = $usersDAOImpl;
    }

    public function validateLoginUser(LoginRequest $loginRequest): void
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
    }
}
