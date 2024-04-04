<?php

namespace Database\DataAccess\Interfaces;

use Models\Like;

interface LikesDAO
{
    public function create(Like $like): Like;
    public function isLiked(int $userId, int $tweetId): bool;
    public function getLikeCountByTweet(int $tweetId): int;
    public function delete(int $userId, int $tweetId): bool;
}
