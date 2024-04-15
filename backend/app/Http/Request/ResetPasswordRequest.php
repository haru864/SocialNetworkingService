<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;

class ResetPasswordRequest
{
    private string $action;
    private ?string $username = null;
    private ?string $email = null;
    private ?string $newPassword = null;
    private ?string $hash = null;

    public function __construct($data)
    {
        $requiredParamsByAction = [
            "send_email" => ['username', 'email'],
            "reset_password" => ['new_password', 'hash']
        ];
        if (!array_key_exists('action', $data)) {
            throw new InvalidRequestParameterException("'action' must be set in request.");
        }
        if (!array_key_exists($data['action'], $requiredParamsByAction)) {
            throw new InvalidRequestParameterException("'action' must be set 'send_email' or 'reset_password'.");
        }
        $this->action = $data['action'];
        foreach ($requiredParamsByAction[$this->action] as $requiredParam) {
            if (is_null($data[$requiredParam])) {
                throw new InvalidRequestParameterException("'{$requiredParam}' must be set in request.");
            }
        }
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->newPassword = $data['new_password'];
        $this->hash = $data['hash'];
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }
}
