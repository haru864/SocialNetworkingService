<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;

class LikeRequest
{
    private string $tweetId;

    public function __construct(array $postData)
    {
        $this->tweetId = $postData['tweet_id'];
        if (is_null($this->tweetId)) {
            throw new InvalidRequestParameterException("'tweet_id' must be set in like-request.");
        }
    }

    public function getTweetId(): string
    {
        return $this->tweetId;
    }
}
