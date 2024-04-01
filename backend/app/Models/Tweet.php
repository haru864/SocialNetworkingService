<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class Tweet implements Model
{
    use GenericModel;

    private ?int $id;
    private ?int $replyToId;
    private int $userId;
    private string $message;
    private ?string $mediaFilePath;
    private ?string $mediaType;
    private string $postingDatetime;

    public function __construct(
        ?int $id,
        ?int $replyToId,
        int $userId,
        string $message,
        ?string $mediaFilePath,
        ?string $mediaType,
        string $postingDatetime
    ) {
        $this->id = $id;
        $this->replyToId = $replyToId;
        $this->userId = $userId;
        $this->message = $message;
        $this->mediaFilePath = $mediaFilePath;
        $this->mediaType = $mediaType;
        $this->postingDatetime = $postingDatetime;
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

    public function getMediaFilePath(): ?string
    {
        return $this->mediaFilePath;
    }

    public function setMediaFilePath(?string $mediaFilePath): void
    {
        $this->mediaFilePath = $mediaFilePath;
    }

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    public function setMediaType(?string $mediaType): void
    {
        $this->mediaType = $mediaType;
    }

    public function getPostingDatetime(): string
    {
        return $this->postingDatetime;
    }

    public function setPostingDatetime(string $postingDatetime): void
    {
        $this->postingDatetime = $postingDatetime;
    }
}
