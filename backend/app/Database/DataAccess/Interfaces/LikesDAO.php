<?php

namespace Database\DataAccess\Interfaces;

use Models\Like;

interface LikesDAO
{
    public function create(Like $like): Like;
    public function getLikeCount(int $tweetId): int;
    public function delete(int $userId, int $tweetId): bool;
}
