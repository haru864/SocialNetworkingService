<?php

namespace Services;

use Database\DataAccess\Implementations\FollowsDAOImpl;
use Models\Follow;

class FollowService
{
    private FollowsDAOImpl $followsDAOImpl;

    public function __construct(FollowsDAOImpl $followsDAOImpl)
    {
        $this->followsDAOImpl = $followsDAOImpl;
    }

    public function addFollow(int $followerId, int $followeeId): void
    {
        $follow = new Follow(
            id: null,
            followerId: $followerId,
            followeeId: $followeeId,
            followDatetime: date('Y-m-d H:i:s')
        );
        $this->followsDAOImpl->create($follow);
        return;
    }

    public function removeFollow(int $followerId, int $followeeId): void
    {
        $this->followsDAOImpl->delete($followerId, $followeeId);
        return;
    }

    public function getFollowers(int $followeeId): array
    {
        $follows = $this->followsDAOImpl->getFollowers($followeeId);
        if (is_null($follows)) {
            $follows = [];
        }
        $userIds = ["user_id" => []];
        foreach ($follows as $follow) {
            $userId = $follow->getFollowerId();
            array_push($userIds['user_id'], $userId);
        }
        return $userIds;
    }

    public function getFollowees(int $followerId): array
    {
        $follows = $this->followsDAOImpl->getFollowees($followerId);
        if (is_null($follows)) {
            $follows = [];
        }
        $userIds = ["user_id" => []];
        foreach ($follows as $follow) {
            $userId = $follow->getFolloweeId();
            array_push($userIds['user_id'], $userId);
        }
        return $userIds;
    }
}
