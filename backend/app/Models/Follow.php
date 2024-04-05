<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class Follow implements Model
{
    use GenericModel;

    private ?int $id;
    private int $followerId;
    private int $followeeId;
    private string $followDatetime;

    public function __construct(
        ?int $id,
        int $followerId,
        int $followeeId,
        string $followDatetime
    ) {
        $this->id = $id;
        $this->followerId = $followerId;
        $this->followeeId = $followeeId;
        $this->followDatetime = $followDatetime;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getFollowerId(): int
    {
        return $this->followerId;
    }

    public function setFollowerId(int $followerId): void
    {
        $this->followerId = $followerId;
    }

    public function getFolloweeId(): int
    {
        return $this->followeeId;
    }

    public function setFolloweeId(int $followeeId): void
    {
        $this->$followeeId = $followeeId;
    }

    public function getFollowDatetime(): string
    {
        return $this->followDatetime;
    }

    public function setFollowDatetime(string $followDatetime): void
    {
        $this->$followDatetime = $followDatetime;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->getId(),
            'followerId' => $this->getFollowerId(),
            'followeeId' => $this->getFolloweeId(),
            'followDatetime' => $this->getFollowDatetime(),
        ];
        return $data;
    }
}
