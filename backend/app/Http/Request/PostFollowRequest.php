<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;

class PostFollowRequest
{
    private string $action;
    private ?int $followeeId;

    public function __construct(array $postData)
    {
        $validActions = ['add', 'remove'];
        if (!in_array($postData['action'], $validActions)) {
            $validActionStr = implode(' or ', $validActions);
            throw new InvalidRequestParameterException("'action' must be {$validActionStr}.");
        }
        if (is_null($postData['followee_id'])) {
            throw new InvalidRequestParameterException("'followee_id' must be set in follow-request.");
        }
        $this->action = $postData['action'];
        $this->followeeId = $postData['followee_id'];
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
