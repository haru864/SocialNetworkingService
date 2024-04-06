<?php

namespace Services;

use Database\DataAccess\Implementations\LikesDAOImpl;
use Models\Like;

class LikeService
{
    private LikesDAOImpl $likesDAOImpl;

    public function __construct(LikesDAOImpl $likesDAOImpl)
    {
        $this->likesDAOImpl = $likesDAOImpl;
    }

    public function addLike(int $tweetId): void
    {
        $userId = $_SESSION['user_id'];
        $like = new Like(
            id: null,
            userId: $userId,
            tweetId: $tweetId,
            likeDatetime: date('Y-m-d H:i:s')
        );
        $this->likesDAOImpl->create($like);
        return;
    }

    public function removeLike(int $tweetId): void
    {
        $userId = $_SESSION['user_id'];
        $this->likesDAOImpl->delete($userId, $tweetId);
        return;
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
}
