<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class Retweet implements Model
{
    use GenericModel;

    private ?int $id;
    private int $userId;
    private int $tweetId;
    private string $retweetDatetime;

    public function __construct(
        ?int $id,
        int $userId,
        int $tweetId,
        string $retweetDatetime
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->tweetId = $tweetId;
        $this->retweetDatetime = $retweetDatetime;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getTweetId(): int
    {
        return $this->tweetId;
    }

    public function setTweetId(int $tweetId): void
    {
        $this->tweetId = $tweetId;
    }

    public function getRetweetDatetime(): string
    {
        return $this->retweetDatetime;
    }

    public function setRetweetDatetime(string $retweetDatetime): void
    {
        $this->retweetDatetime = $retweetDatetime;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'tweetId' => $this->getTweetId(),
            'retweetDatetime' => $this->getRetweetDatetime()
        ];
        return $data;
    }
}
