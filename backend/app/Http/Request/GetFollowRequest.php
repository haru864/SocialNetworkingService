<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;

class GetFollowRequest
{
    private string $type;
    private ?int $followId;
    private ?int $userId;
    private ?int $page;
    private ?int $limit;

    public function __construct(array $getData)
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->type = explode('/', $uriDir)[3];
        $validTypes = ['follower', 'followee', 'follow'];
        if (!in_array($this->type, $validTypes)) {
            throw new InvalidRequestParameterException('Invalid Request URL for follow-API.');
        }
        $this->followId = $getData['follow_id'];
        $this->userId = $getData['user_id'];
        $this->page = $getData['page'];
        $this->limit = $getData['limit'];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFollowId(): ?int
    {
        return $this->followId;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
