<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class User implements Model
{
    use GenericModel;

    public ?int $id;
    public string $name;
    public string $password_hash;
    public string $email;
    public ?string $self_introduction;
    public ?string $profile_image;
    public string $created_at;
    public string $last_login;
    public ?string $email_verified_at;

    public function __construct(
        ?int $id,
        string $name,
        string $password_hash,
        string $email,
        ?string $self_introduction,
        ?string $profile_image,
        string $created_at,
        string $last_login,
        ?string $email_verified_at
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->password_hash = $password_hash;
        $this->email = $email;
        $this->self_introduction = $self_introduction;
        $this->profile_image = $profile_image;
        $this->created_at = $created_at;
        $this->last_login = $last_login;
        $this->email_verified_at = $email_verified_at;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    public function setPasswordHash(string $password_hash): void
    {
        $this->password_hash = $password_hash;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getSelfIntroduction(): ?string
    {
        return $this->self_introduction;
    }

    public function setSelfIntroduction(?string $self_introduction): void
    {
        $this->self_introduction = $self_introduction;
    }

    public function getProfileImage(): ?string
    {
        return $this->profile_image;
    }

    public function setProfileImage(?string $profile_image): void
    {
        $this->profile_image = $profile_image;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getLastLogin(): string
    {
        return $this->last_login;
    }

    public function setLastLogin(string $last_login): void
    {
        $this->last_login = $last_login;
    }

    public function getEmailVerifiedAt(): ?string
    {
        return $this->email_verified_at;
    }

    public function setEmailVerifiedAt(?string $email_verified_at): void
    {
        $this->email_verified_at = $email_verified_at;
    }
}
