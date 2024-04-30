<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Helpers\ValidationHelper;

class GetLikeRequest
{
    private int $tweetId;

    public function __construct(array $getData)
    {
        if (!ValidationHelper::isPositiveIntegerString($getData['tweet_id'])) {
            throw new InvalidRequestParameterException("'tweet_id' must be positive integer.");
        }
        $this->tweetId = (int)$getData['tweet_id'];
    }

    public function getTweetId(): int
    {
        return $this->tweetId;
    }
}
