<?php

namespace Database\DataTransfer;

class NotificationDTO
{
    private String $notificationType;
    private int $id;
    private int $notifiedUserId;
    private int $entityId;
    private bool $isConfirmed;
    private string $createdAt;

    public function __construct(
        String $notificationType,
        int $id,
        int $notifiedUserId,
        int $entityId,
        bool $isConfirmed,
        string $createdAt
    ) {
        $this->notificationType = $notificationType;
        $this->id = $id;
        $this->notifiedUserId = $notifiedUserId;
        $this->entityId = $entityId;
        $this->isConfirmed = $isConfirmed;
        $this->createdAt = $createdAt;
    }

    public function getNotificationType(): string
    {
        return $this->notificationType;
    }

    public function setNotificationType(string $notificationType): void
    {
        $this->notificationType = $notificationType;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
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

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): void
    {
        $this->entityId = $entityId;
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
            'notificationType' => $this->getNotificationType(),
            'id' => $this->getId(),
            'notifiedUserId' => $this->getNotifiedUserId(),
            'entityId' => $this->getEntityId(),
            'isConfirmed' => $this->getIsConfirmed(),
            'createdAt' => $this->getCreatedAt()
        ];
        return $data;
    }
}
