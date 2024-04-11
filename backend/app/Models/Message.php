<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class Message implements Model
{
    use GenericModel;

    private ?int $id;
    private int $senderId;
    private int $recipientId;
    private string $message;
    private ?string $mediaFileName;
    private ?string $mediaType;
    private string $sendDatetime;

    public function __construct(
        ?int $id,
        int $senderId,
        int $recipientId,
        string $message,
        ?string $mediaFileName,
        ?string $mediaType,
        string $sendDatetime
    ) {
        $this->id = $id;
        $this->senderId = $senderId;
        $this->recipientId = $recipientId;
        $this->message = $message;
        $this->mediaFileName = $mediaFileName;
        $this->mediaType = $mediaType;
        $this->sendDatetime = $sendDatetime;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getSenderId(): ?int
    {
        return $this->senderId;
    }

    public function setSenderId(?int $senderId): void
    {
        $this->senderId = $senderId;
    }

    public function getRecipientId(): int
    {
        return $this->recipientId;
    }

    public function setRecipientId(int $recipientId): void
    {
        $this->recipientId = $recipientId;
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

    public function getSendDatetime(): string
    {
        return $this->sendDatetime;
    }

    public function setSendDatetime(string $sendDatetime): void
    {
        $this->sendDatetime = $sendDatetime;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->getId(),
            'senderId' => $this->getSenderId(),
            'recipientId' => $this->getRecipientId(),
            'message' => $this->getMessage(),
            'mediaFileName' => $this->getMediaFileName(),
            'mediaType' => $this->getMediaType(),
            'sendDatetime' => $this->getSendDatetime()
        ];
        return $data;
    }
}
