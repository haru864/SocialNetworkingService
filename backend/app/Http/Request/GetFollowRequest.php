<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;

class GetFollowRequest
{
    private string $type;
    private ?int $userId;
    private int $page;
    private int $limit;

    public function __construct(array $getData)
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->type = explode('/', $uriDir)[3];
        $validTypes = ['follower', 'followee'];
        if (!in_array($this->type, $validTypes)) {
            throw new InvalidRequestParameterException('Invalid Request URL for follow-API.');
        }
        $requiredParams = ['page', 'limit'];
        foreach ($requiredParams as $param) {
            if (is_null($getData[$param])) {
                throw new InvalidRequestParameterException("'{$param}' must be set in get-tweets request.");
            }
        }
        $this->userId = $getData['user_id'];
        $this->page = $getData['page'];
        $this->limit = $getData['limit'];
    }

    public function getType(): string
    {
        return $this->type;
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
