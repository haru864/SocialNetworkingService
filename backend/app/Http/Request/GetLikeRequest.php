<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Helpers\ValidationHelper;

class GetLikeRequest
{
    private ?int $tweetId;
    private ?int $likeId;

    public function __construct(array $getData)
    {
        if (is_null($getData['tweet_id']) && is_null($getData['like_id'])) {
            throw new InvalidRequestParameterException("Set 'tweet_id' or 'like_id' at query string.");
        }
        if (isset($getData['tweet_id']) && isset($getData['like_id'])) {
            throw new InvalidRequestParameterException("Set only one of 'tweet_id' or 'like_id' at query string.");
        }

        if (isset($getData['tweet_id']) && !ValidationHelper::isPositiveIntegerString($getData['tweet_id'])) {
            throw new InvalidRequestParameterException("'tweet_id' must be positive integer.");
        }
        if (isset($getData['like_id']) && !ValidationHelper::isPositiveIntegerString($getData['like_id'])) {
            throw new InvalidRequestParameterException("'like_id' must be positive integer.");
        }

        $this->tweetId = $getData['tweet_id'];
        $this->likeId = $getData['like_id'];
    }

    public function getTweetId(): ?int
    {
        return $this->tweetId;
    }

    public function getLikeId(): ?int
    {
        return $this->likeId;
    }
}
