<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class Career implements Model
{
    use GenericModel;

    public ?int $id;
    public int $userId;
    public string $job;

    public function __construct(
        ?int $id,
        int $userId,
        string $job
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->job = $job;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
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

    public function getJob(): string
    {
        return $this->job;
    }

    public function setJob(string $job): void
    {
        $this->job = $job;
    }
}
