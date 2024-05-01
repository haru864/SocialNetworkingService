<?php

namespace Services;

use Database\DataAccess\Implementations\RetweetsDAOImpl;
use Exceptions\InvalidRequestParameterException;
use Models\Retweet;

class RetweetService
{
    private RetweetsDAOImpl $retweetsDAOImpl;

    public function __construct(RetweetsDAOImpl $retweetsDAOImpl)
    {
        $this->retweetsDAOImpl = $retweetsDAOImpl;
    }

    public function createRetweet(int $userId, int $tweetId, ?string $message): void
    {
        $retweetInTable = $this->retweetsDAOImpl->getRetweet($userId, $tweetId);
        if (!is_null($retweetInTable)) {
            throw new InvalidRequestParameterException("Already retweeted.");
        }
        $retweet = new Retweet(
            id: null,
            userId: $userId,
            tweetId: $tweetId,
            message: $message,
            retweetDatetime: date('Y-m-d H:i:s')
        );
        $this->retweetsDAOImpl->create($retweet);
        return;
    }

    public function removeRetweet(int $userId, int $tweetId): void
    {
        $retweetInTable = $this->retweetsDAOImpl->getRetweet($userId, $tweetId);
        if (is_null($retweetInTable)) {
            throw new InvalidRequestParameterException("Not retweeted.");
        }
        $this->retweetsDAOImpl->deleteById($retweetInTable->getId());
        return;
    }

    public function getRetweets(int $tweetId): ?array
    {
        $retweets = $this->retweetsDAOImpl->getByTweetId($tweetId);
        $retweetArr = [];
        foreach ($retweets as $retweet) {
            array_push($retweetArr, $retweet->toArray());
        }
        return $retweetArr;
    }
}
