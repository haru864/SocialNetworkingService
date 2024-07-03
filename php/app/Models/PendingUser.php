<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class PendingUser implements Model
{
    use GenericModel;

    private ?int $id;
    private ?int $userId;
    private string $name;
    private string $password_hash;
    private string $email;
    private ?string $self_introduction;
    private ?string $profile_image;

    public function __construct(
        ?int $id,
        ?int $userId,
        string $name,
        string $password_hash,
        string $email,
        ?string $self_introduction,
        ?string $profile_image,
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->name = $name;
        $this->password_hash = $password_hash;
        $this->email = $email;
        $this->self_introduction = $self_introduction;
        $this->profile_image = $profile_image;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
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

    public function toArray(): array
    {
        $data = [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "password_hash" => "",
            "email" => $this->getEmail(),
            "self_introduction" => $this->getSelfIntroduction(),
            "profile_image" => $this->getProfileImage()
        ];
        return $data;
    }
}
