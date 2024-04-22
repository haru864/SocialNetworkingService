<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class EmailVerification implements Model
{
    use GenericModel;

    private string $hash;
    private int $pendingUserId;
    private string $createdAt;
    private string $expiredAt;

    public function __construct(
        string $hash,
        int $pendingUserId,
        string $createdAt,
        string $expiredAt
    ) {
        $this->hash = $hash;
        $this->pendingUserId = $pendingUserId;
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

    public function getPendingUserId(): int
    {
        return $this->pendingUserId;
    }

    public function setPendingUserId(int $pendingUserId): void
    {
        $this->pendingUserId = $pendingUserId;
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
