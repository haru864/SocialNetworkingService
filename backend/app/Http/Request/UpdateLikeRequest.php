<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Helpers\ValidationHelper;

class UpdateLikeRequest
{
    private string $action;
    private int $tweetId;

    public function __construct(array $postData)
    {
        $validActions = ['add', 'remove'];
        if (!in_array($postData['action'], $validActions)) {
            throw new InvalidRequestParameterException("Given 'action' is invalid.");
        }
        if (is_null($postData['tweet_id'])) {
            throw new InvalidRequestParameterException("'tweet_id' must be set.");
        }
        if (!ValidationHelper::isPositiveIntegerString($postData['tweet_id'])) {
            throw new InvalidRequestParameterException("'tweet_id' must be positive integer.");
        }
        $this->action = $postData['action'];
        $this->tweetId = (int)$postData['tweet_id'];
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getTweetId(): int
    {
        return $this->tweetId;
    }
}
