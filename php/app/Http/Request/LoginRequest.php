<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;

class LoginRequest
{
    private string $username;
    private string $password;

    public function __construct(array $data)
    {
        $requiredParams = ['username', 'password'];
        foreach ($requiredParams as $requiredParam) {
            if (is_null($data[$requiredParam])) {
                throw new InvalidRequestParameterException("'{$requiredParam}' must be set.");
            }
        }
        $this->username = $data['username'];
        $this->password = $data['password'];
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
