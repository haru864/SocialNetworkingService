<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class PendingHobby implements Model
{
    use GenericModel;

    private ?int $id;
    private int $pendingUserId;
    private string $hobby;

    public function __construct(
        ?int $id,
        int $pendingUserId,
        string $hobby
    ) {
        $this->id = $id;
        $this->pendingUserId = $pendingUserId;
        $this->hobby = $hobby;
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

    public function getHobby(): string
    {
        return $this->hobby;
    }

    public function setHobby(string $hobby): void
    {
        $this->$hobby = $hobby;
    }
}
