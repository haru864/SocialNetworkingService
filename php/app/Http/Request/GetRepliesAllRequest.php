<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Helpers\ValidationHelper;

class GetRepliesAllRequest
{
    private string $tweetId;

    public function __construct()
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->tweetId = explode('/', $uriDir)[3];
        if (!ValidationHelper::isPositiveIntegerString($this->tweetId)) {
            throw new InvalidRequestParameterException("'tweet_id' must be positive integer.");
        }
    }

    public function getTweetId(): string
    {
        return $this->tweetId;
    }
}
