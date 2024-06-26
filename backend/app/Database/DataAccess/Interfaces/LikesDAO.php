<?php

namespace Database\DataAccess\Interfaces;

use Models\Like;

interface LikesDAO
{
    public function create(Like $like): Like;
    public function getLike(int $userId, int $tweetId): ?Like;
    public function getLikeById(int $id): ?Like;
    public function getLikeUsers(int $tweetId): array;
    public function delete(int $userId, int $tweetId): bool;
}
