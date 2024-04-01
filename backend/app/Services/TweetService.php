<?php

namespace Services;

use Database\DataAccess\Implementations\TweetsDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InvalidRequestParameterException;
use Http\Request\LoginRequest;
use Http\Request\PostTweetRequest;
use Models\Tweet;
use Models\User;

class TweetService
{
    private TweetsDAOImpl $tweetsDAOImpl;

    public function __construct(TweetsDAOImpl $tweetsDAOImpl)
    {
        $this->tweetsDAOImpl = $tweetsDAOImpl;
    }

    public function createTweet(PostTweetRequest $request): void
    {
        $currentDatetime = date('Y-m-d H:i:s');
        $tweet = new Tweet(
            id: null,
            replyToId: null,
            userId: $_SESSION['user_id'],
            message: $request->getMessage(),
            mediaFilePath: null,
            mediaType: null,
            postingDatetime: $currentDatetime
        );
    }

    public function getTweetsByUser(): ?array
    {
        return null;
    }

    public function getTweetsByLikes(): ?array
    {
        return null;
    }

    public function getTweetsByFollows(): ?array
    {
        return null;
    }
}
