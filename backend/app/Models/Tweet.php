<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class Tweet implements Model
{
    use GenericModel;

    private ?int $id;
    private ?int $replyToId;
    private ?int $retweetToId;
    private int $userId;
    private string $message;
    private ?string $mediaFileName;
    private ?string $mediaType;
    private string $postingDatetime;

    public function __construct(
        ?int $id,
        ?int $replyToId,
        ?int $retweetToId,
        int $userId,
        string $message,
        ?string $mediaFileName,
        ?string $mediaType,
        string $postingDatetime
    ) {
        $this->id = $id;
        $this->replyToId = $replyToId;
        $this->retweetToId = $retweetToId;
        $this->userId = $userId;
        $this->message = $message;
        $this->mediaFileName = $mediaFileName;
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

    public function getRetweetToId(): ?int
    {
        return $this->retweetToId;
    }

    public function setRetweetToId(?int $retweetToId): void
    {
        $this->retweetToId = $retweetToId;
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

    public function getPostingDatetime(): string
    {
        return $this->postingDatetime;
    }

    public function setPostingDatetime(string $postingDatetime): void
    {
        $this->postingDatetime = $postingDatetime;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->getId(),
            'replyToId' => $this->getReplyToId(),
            'retweetToId' => $this->getRetweetToId(),
            'userId' => $this->getUserId(),
            'message' => $this->getMessage(),
            'mediaFileName' => $this->getMediaFileName(),
            'mediaType' => $this->getMediaType(),
            'postingDatetime' => $this->getPostingDatetime()
        ];
        return $data;
    }
}
