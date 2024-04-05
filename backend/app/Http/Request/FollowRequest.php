<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;

class FollowRequest
{
    private string $action;
    private ?int $followeeId;

    public function __construct(array $data)
    {
        $validActions = ['add_follow', 'remove_follow', 'get_followers', 'get_followees'];
        if (!in_array($data['action'], $validActions)) {
            throw new InvalidRequestParameterException("Given 'action' is invalid.");
        }
        if ($data['action'] === 'add_follow' || $data['action'] === 'remove_follow') {
            if (is_null($data['followee_id'])) {
                throw new InvalidRequestParameterException("'followee_id' must be set in follow-request.");
            }
        }
        $this->action = $data['action'];
        $this->followeeId = $data['followee_id'];
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getFolloweeId(): ?int
    {
        return $this->followeeId;
    }
}
