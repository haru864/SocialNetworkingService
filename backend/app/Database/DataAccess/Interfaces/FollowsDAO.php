<?php

namespace Database\DataAccess\Interfaces;

use Models\Follow;

interface FollowsDAO
{
    public function create(Follow $follow): Follow;
    public function getFollow(int $followerId, int $followeeId): ?Follow;
    public function getFollowers(int $followeeId): ?array;
    public function getFollowees(int $followerId): ?array;
    public function delete(int $followerId, int $followeeId): bool;
}
