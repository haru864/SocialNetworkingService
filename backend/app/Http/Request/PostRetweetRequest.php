<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Helpers\ValidationHelper;

class PostRetweetRequest
{
    private string $tweetId;
    private string $message;

    public function __construct($postData)
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->tweetId = explode('/', $uriDir)[3];
        ValidationHelper::isPositiveIntegerString($this->tweetId);
        if (is_null($postData['message'])) {
            throw new InvalidRequestParameterException("'message' must be set.");
        }
        $this->message = $postData['message'];
        ValidationHelper::validateStringLength($this->message, "retweet-message", 0, 200);
    }

    public function getTweetId(): string
    {
        return $this->tweetId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
