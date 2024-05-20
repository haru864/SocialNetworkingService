<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class MessageNotification implements Model
{
    use GenericModel;

    private ?int $id;
    private int $notifiedUserId;
    private int $messageId;
    private bool $isConfirmed;
    private string $createdAt;

    public function __construct(
        ?int $id,
        int $notifiedUserId,
        int $messageId,
        bool $isConfirmed,
        string $createdAt
    ) {
        $this->id = $id;
        $this->notifiedUserId = $notifiedUserId;
        $this->messageId = $messageId;
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

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function setMessageId(int $messageId): void
    {
        $this->messageId = $messageId;
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
            'messageId' => $this->getMessageId(),
            'isConfirmed' => $this->getIsConfirmed(),
            'createdAt' => $this->getCreatedAt()
        ];
        return $data;
    }
}
