<?php

namespace Database\DataAccess\Interfaces;

use Models\Retweet;

interface RetweetsDAO
{
    public function create(Retweet $retweet): Retweet;
    public function getRetweet(int $userId, int $tweetId): ?Retweet;
    public function getByTweetId(int $tweetId): ?array;
    public function deleteById(int $id): bool;
}
