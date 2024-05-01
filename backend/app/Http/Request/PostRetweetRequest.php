<?php

namespace Http\Request;

use Helpers\ValidationHelper;

class PostRetweetRequest
{
    private string $tweetId;
    private ?string $message;

    public function __construct($postData)
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->tweetId = explode('/', $uriDir)[3];
        ValidationHelper::isPositiveIntegerString($this->tweetId);
        $this->message = $postData['message'];
        if (isset($this->message)) {
            ValidationHelper::validateStringLength($this->message, "retweet-message", 0, 200);
        }
    }

    public function getTweetId(): string
    {
        return $this->tweetId;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
