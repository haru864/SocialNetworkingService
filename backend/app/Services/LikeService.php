<?php

namespace Services;

use Database\DataAccess\Implementations\LikesDAOImpl;
use Exceptions\InvalidRequestParameterException;
use Models\Like;

class LikeService
{
    private LikesDAOImpl $likesDAOImpl;

    public function __construct(LikesDAOImpl $likesDAOImpl)
    {
        $this->likesDAOImpl = $likesDAOImpl;
    }

    public function checkLiked(int $userId, int $tweetId): bool
    {
        $like =  $this->likesDAOImpl->getLike($userId, $tweetId);
        return !is_null($like);
    }

    public function getLikeUsers(int $tweetId): array
    {
        $likes = $this->likesDAOImpl->getLikeUsers($tweetId);
        $userIds = [];
        foreach ($likes as $like) {
            array_push($userIds, $like->getUserId());
        }
        return $userIds;
    }

    public function getLikeData(int $likeId): ?Like
    {
        $like = $this->likesDAOImpl->getLikeById($likeId);
        return $like;
    }

    public function addLike(int $userId, int $tweetId): Like
    {
        if (!is_null($this->likesDAOImpl->getLike($userId, $tweetId))) {
            throw new InvalidRequestParameterException('Tweet has already been liked.');
        }
        $like = new Like(
            id: null,
            userId: $userId,
            tweetId: $tweetId,
            likeDatetime: date('Y-m-d H:i:s')
        );
        $likeInTable = $this->likesDAOImpl->create($like);
        return $likeInTable;
    }

    public function removeLike(int $userId, int $tweetId): void
    {
        if (is_null($this->likesDAOImpl->getLike($userId, $tweetId))) {
            throw new InvalidRequestParameterException('Tweet has not been liked.');
        }
        $this->likesDAOImpl->delete($userId, $tweetId);
        return;
    }
}
