<?php

namespace Http\Request;

class GetFollowRequest
{
    private ?int $userId;
    private string $relation;

    public function __construct(array $getData)
    {
        $this->userId = $getData['id'];
        $this->relation = $getData['relation'];
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }
}
