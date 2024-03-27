<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;

class LoginRequest
{
    private string $username;
    private string $password;

    public function __construct(array $data)
    {
        $this->username = $data['username'] ?? null;
        $this->password = $data['password'] ?? null;
        if (is_null($this->username)) {
            throw new InvalidRequestParameterException("'username' must be set in login-request.");
        }
        if (is_null($this->password)) {
            throw new InvalidRequestParameterException("'password' must be set in login-request.");
        }
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
