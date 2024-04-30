<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class ScheduledTweet implements Model
{
    use GenericModel;

    private ?int $id;
    private ?int $replyToId;
    private int $userId;
    private string $message;
    private ?string $mediaFileName;
    private ?string $mediaType;
    private string $scheduledDatetime;

    public function __construct(
        ?int $id,
        ?int $replyToId,
        int $userId,
        string $message,
        ?string $mediaFileName,
        ?string $mediaType,
        string $scheduledDatetime
    ) {
        $this->id = $id;
        $this->replyToId = $replyToId;
        $this->userId = $userId;
        $this->message = $message;
        $this->mediaFileName = $mediaFileName;
        $this->mediaType = $mediaType;
        $this->scheduledDatetime = $scheduledDatetime;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getReplyToId(): ?int
    {
        return $this->replyToId;
    }

    public function setReplyToId(?int $replyToId): void
    {
        $this->replyToId = $replyToId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getMediaFileName(): ?string
    {
        return $this->mediaFileName;
    }

    public function setMediaFileName(?string $mediaFileName): void
    {
        $this->mediaFileName = $mediaFileName;
    }

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    public function setMediaType(?string $mediaType): void
    {
        $this->mediaType = $mediaType;
    }

    public function getScheduledDatetime(): string
    {
        return $this->scheduledDatetime;
    }

    public function setScheduledDatetime(string $scheduledDatetime): void
    {
        $this->scheduledDatetime = $scheduledDatetime;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->getId(),
            'replyToId' => $this->getReplyToId(),
            'userId' => $this->getUserId(),
            'message' => $this->getMessage(),
            'mediaFileName' => $this->getMediaFileName(),
            'mediaType' => $this->getMediaType(),
            'scheduledDatetime' => $this->getScheduledDatetime()
        ];
        return $data;
    }
}
