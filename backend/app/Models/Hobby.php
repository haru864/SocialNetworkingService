<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class Hobby implements Model
{
    use GenericModel;

    private ?int $id;
    private int $userId;
    private string $hobby;

    public function __construct(
        ?int $id,
        int $userId,
        string $hobby
    ) {
        $this->id = $id;
        $this->userId = $userId;
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

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
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
