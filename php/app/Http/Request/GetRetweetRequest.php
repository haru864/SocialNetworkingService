<?php

namespace Http\Request;

use Helpers\ValidationHelper;

class GetRetweetRequest
{
    private string $tweetId;
    private ?int $retweetId;

    public function __construct(array $getData)
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->tweetId = explode('/', $uriDir)[3];
        ValidationHelper::isPositiveIntegerString($this->tweetId);
        $this->retweetId = $getData['retweet_id'];
    }

    public function getTweetId(): string
    {
        return $this->tweetId;
    }

    public function getRetweetId(): ?int
    {
        return $this->retweetId;
    }
}
