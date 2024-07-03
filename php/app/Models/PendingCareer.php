<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class PendingCareer implements Model
{
    use GenericModel;

    private ?int $id;
    private int $pendingUserId;
    private string $job;

    public function __construct(
        ?int $id,
        int $pendingUserId,
        string $job
    ) {
        $this->id = $id;
        $this->pendingUserId = $pendingUserId;
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

    public function getPendingUserId(): int
    {
        return $this->pendingUserId;
    }

    public function setPendingUserId(int $pendingUserId): void
    {
        $this->pendingUserId = $pendingUserId;
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
