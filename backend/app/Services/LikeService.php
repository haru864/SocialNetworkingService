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

    public function getLikeUsers(int $tweetId): array
    {
        $likes = $this->likesDAOImpl->getLikeUsers($tweetId);
        $userIds = [];
        foreach ($likes as $like) {
            array_push($userIds, $like->getUserId());
        }
        return $userIds;
    }

    public function addLike(int $userId, int $tweetId): void
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
        $this->likesDAOImpl->create($like);
        return;
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
