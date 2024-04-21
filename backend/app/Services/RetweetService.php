<?php

namespace Services;

use Database\DataAccess\Implementations\RetweetsDAOImpl;
use Exceptions\InvalidRequestParameterException;
use Helpers\SessionManager;
use Http\Request\RetweetsRequest;
use Models\Retweet;

class RetweetService
{
    private RetweetsDAOImpl $retweetsDAOImpl;

    public function __construct(RetweetsDAOImpl $retweetsDAOImpl)
    {
        $this->retweetsDAOImpl = $retweetsDAOImpl;
    }

    public function createRetweet(RetweetsRequest $request, int $userId): void
    {
        $tweetId = $request->getTweetId();
        $retweetInTable = $this->retweetsDAOImpl->getRetweet($userId, $tweetId);
        if (!is_null($retweetInTable)) {
            throw new InvalidRequestParameterException("Already retweeted.");
        }
        $retweet = new Retweet(
            id: null,
            userId: $userId,
            tweetId: $tweetId,
            retweetDatetime: date('Y-m-d H:i:s')
        );
        $this->retweetsDAOImpl->create($retweet);
        return;
    }

    public function getRetweets(RetweetsRequest $request): ?array
    {
        $tweetId = $request->getTweetId();
        $retweets = $this->retweetsDAOImpl->getByTweetId($tweetId);
        $retweetArr = [];
        foreach ($retweets as $retweet) {
            array_push($retweetArr, $retweet->toArray());
        }
        return $retweetArr;
    }
}
