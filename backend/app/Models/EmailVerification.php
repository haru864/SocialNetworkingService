<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class EmailVerification implements Model
{
    use GenericModel;

    public string $hash;
    public int $userId;
    public string $createdAt;
    public string $expiredAt;

    public function __construct(
        string $hash,
        int $userId,
        string $createdAt,
        string $expiredAt
    ) {
        $this->hash = $hash;
        $this->userId = $userId;
        $this->createdAt = $createdAt;
        $this->expiredAt = $expiredAt;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getExpiredAt(): string
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(string $expiredAt): void
    {
        $this->expiredAt = $expiredAt;
    }
}
