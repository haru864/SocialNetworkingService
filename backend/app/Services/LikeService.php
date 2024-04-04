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

    public function updateLike(int $tweetId): void
    {
        $userId = $_SESSION['user_id'];
        $isLiked = $this->likesDAOImpl->isLiked($userId, $tweetId);
        if ($isLiked) {
            $this->likesDAOImpl->delete($userId, $tweetId);
        } else {
            $like = new Like(
                id: null,
                userId: $userId,
                tweetId: $tweetId,
                likeDatetime: date('Y-m-d H:i:s')
            );
            $this->likesDAOImpl->create($like);
        }
        return;
    }
}
