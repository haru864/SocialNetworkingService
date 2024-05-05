<?php

namespace Database\DataAccess\Interfaces;

use Models\Tweet;

interface TweetsDAO
{
    public function create(Tweet $tweet): Tweet;
    public function getByTweetId(int $id): ?Tweet;
    public function getReplies(int $replyToId, int $limit, int $offset): ?array;
    public function getRetweets(int $tweetId): ?array;
    public function getRetweetByUser(int $userId, int $tweetId): ?array;
    public function getByUserId(int $userId, int $limit, int $offset): ?array;
    public function getByFollower(int $userId, int $limit, int $offset): ?array;
    public function getByPopular(int $limit, int $offset): ?array;
    public function deleteById(int $id): bool;
}
