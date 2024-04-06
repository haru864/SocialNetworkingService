<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;

class LikeRequest
{
    private string $action;
    private ?string $tweetId;

    public function __construct(array $data)
    {
        $validActions = ['add_like', 'remove_like', 'get_users'];
        if (!in_array($data['action'], $validActions)) {
            throw new InvalidRequestParameterException("Given 'action' is invalid.");
        }
        if (is_null($data['tweet_id'])) {
            throw new InvalidRequestParameterException("'tweet_id' must be set.");
        }
        $this->action = $data['action'];
        $this->tweetId = $data['tweet_id'];
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getTweetId(): ?string
    {
        return $this->tweetId;
    }
}
