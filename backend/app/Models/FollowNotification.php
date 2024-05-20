<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class FollowNotification implements Model
{
    use GenericModel;

    private ?int $id;
    private int $notifiedUserId;
    private int $followId;
    private bool $isConfirmed;
    private string $createdAt;

    public function __construct(
        ?int $id,
        int $notifiedUserId,
        int $followId,
        bool $isConfirmed,
        string $createdAt
    ) {
        $this->id = $id;
        $this->notifiedUserId = $notifiedUserId;
        $this->followId = $followId;
        $this->isConfirmed = $isConfirmed;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNotifiedUserId(): int
    {
        return $this->notifiedUserId;
    }

    public function setNotifiedUserId(int $notifiedUserId): void
    {
        $this->notifiedUserId = $notifiedUserId;
    }

    public function getFollowId(): int
    {
        return $this->followId;
    }

    public function setFollowId(int $followId): void
    {
        $this->followId = $followId;
    }

    public function getIsConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): void
    {
        $this->isConfirmed = $isConfirmed;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->getId(),
            'notifiedUserId' => $this->getNotifiedUserId(),
            'followId' => $this->getFollowId(),
            'isConfirmed' => $this->getIsConfirmed(),
            'createdAt' => $this->getCreatedAt()
        ];
        return $data;
    }
}
