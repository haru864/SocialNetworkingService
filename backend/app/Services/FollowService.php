<?php

namespace Services;

use Database\DataAccess\Implementations\FollowsDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InvalidRequestParameterException;
use Models\Follow;

class FollowService
{
    private UsersDAOImpl $usersDAOImpl;
    private FollowsDAOImpl $followsDAOImpl;

    public function __construct(UsersDAOImpl $usersDAOImpl, FollowsDAOImpl $followsDAOImpl)
    {
        $this->followsDAOImpl = $followsDAOImpl;
        $this->usersDAOImpl = $usersDAOImpl;
    }

    public function addFollow(int $followerId, int $followeeId): void
    {
        if ($followerId === $followeeId) {
            throw new InvalidRequestParameterException('Cannot follow yourself.');
        }
        if (
            is_null($this->usersDAOImpl->getById($followerId))
            || is_null($this->usersDAOImpl->getById($followeeId))
        ) {
            throw new InvalidRequestParameterException("Specified user does not exist. (follower:{$followerId}, followee:{$followeeId})");
        }
        if (!is_null($this->followsDAOImpl->getFollow($followerId, $followeeId))) {
            throw new InvalidRequestParameterException("Already following. (follower:{$followerId}, followee:{$followeeId})");
        }
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

    public function getFollowers(int $followeeId, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $follows = $this->followsDAOImpl->getFollowers($followeeId, $limit, $offset);
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

    public function getFollowees(int $followerId, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $follows = $this->followsDAOImpl->getFollowees($followerId, $limit, $offset);
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
