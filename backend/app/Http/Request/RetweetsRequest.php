<?php

namespace Http\Request;

class RetweetsRequest
{
    private string $tweetId;

    public function __construct()
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->tweetId = explode('/', $uriDir)[3];
    }

    public function getTweetId(): string
    {
        return $this->tweetId;
    }
}
