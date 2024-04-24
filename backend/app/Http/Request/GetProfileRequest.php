<?php

namespace Http\Request;

class GetProfileRequest
{
    private ?int $userId;

    public function __construct($getData)
    {
        $this->userId = $getData['id'];
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }
}
