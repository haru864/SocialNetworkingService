<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\LikesDAO;
use Database\DatabaseManager;
use Models\Like;
use Exceptions\QueryFailedException;

class LikesDAOImpl implements LikesDAO
{
    public function create(Like $like): Like
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO likes (
                user_id,
                tweet_id,
                like_datetime
            )
            VALUES (
                ?, ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'iis',
            [
                $like->getUserId(),
                $like->getTweetId(),
                $like->getLikeDatetime(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT INTO failed.");
        }
        $like->setId($mysqli->insert_id);
        return $like;
    }

    public function isLiked(int $userId, int $tweetId): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM likes WHERE user_id = ? AND tweet_id = ?";
        $records = $mysqli->prepareAndFetchAll($query, 'ii', [$userId, $tweetId]);
        return count($records) > 0;
    }

    public function getLikeCountByTweet(int $tweetId): int
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT count(*) FROM likes WHERE tweet_id = ?";
        $count = $mysqli->prepareAndFetchAll($query, 'i', [$tweetId])[0];
        return $count;
    }

    public function delete(int $userId, int $tweetId): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM likes WHERE user_id = ? AND tweet_id = ?";
        return $mysqli->prepareAndExecute($sql, 'ii', [$userId, $tweetId]);
    }
}
