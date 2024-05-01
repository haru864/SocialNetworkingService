<?php

namespace Http\Request;

use Helpers\ValidationHelper;

class GetTweetRequest
{
    private string $tweetId;

    public function __construct()
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->tweetId = explode('/', $uriDir)[3];
        ValidationHelper::isPositiveIntegerString($this->tweetId);
    }

    public function getTweetId(): string
    {
        return $this->tweetId;
    }
}
