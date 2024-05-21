<?php

namespace Services;

use Database\DataAccess\Implementations\TweetsDAOImpl;
use Exceptions\InternalServerException;
use Exceptions\InvalidRequestParameterException;
use Models\Tweet;

class RetweetService
{
    private TweetsDAOImpl $tweetsDAOImpl;

    public function __construct(TweetsDAOImpl $tweetsDAOImpl)
    {
        $this->tweetsDAOImpl = $tweetsDAOImpl;
    }

    public function createRetweet(int $userId, int $tweetId, ?string $message): Tweet
    {
        $retweetInTable = $this->tweetsDAOImpl->getRetweetByUser($userId, $tweetId);
        if (count($retweetInTable) === 1) {
            throw new InvalidRequestParameterException("Already retweeted.");
        } else if (count($retweetInTable) > 1) {
            throw new InternalServerException("Multiple retweets to the same tweet.");
        }
        $retweet = new Tweet(
            id: null,
            replyToId: null,
            retweetToId: $tweetId,
            userId: $userId,
            message: $message,
            mediaFileName: null,
            mediaType: null,
            postingDatetime: date('Y-m-d H:i:s')
        );
        $retweetInTable = $this->tweetsDAOImpl->create($retweet);
        return $retweetInTable;
    }

    public function removeRetweet(int $userId, int $tweetId): void
    {
        $retweetInTable = $this->tweetsDAOImpl->getRetweetByUser($userId, $tweetId);
        if (count($retweetInTable) === 0) {
            throw new InvalidRequestParameterException("Not retweeted.");
        } else if (count($retweetInTable) > 1) {
            throw new InternalServerException("Multiple retweets to the same tweet.");
        }
        $this->tweetsDAOImpl->deleteById($retweetInTable[0]->getId());
        return;
    }

    public function getRetweets(int $tweetId): ?array
    {
        $retweets = $this->tweetsDAOImpl->getRetweets($tweetId);
        $retweetArr = [];
        foreach ($retweets as $retweet) {
            array_push($retweetArr, $retweet->toArray());
        }
        return $retweetArr;
    }
}
