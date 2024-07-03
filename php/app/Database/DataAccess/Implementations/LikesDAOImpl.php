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

    public function getLike(int $userId, int $tweetId): ?Like
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM likes WHERE user_id = ? AND tweet_id = ?";
        $records = $mysqli->prepareAndFetchAll($query, 'ii', [$userId, $tweetId]);
        return count($records) === 0 ? null : $this->convertRecordToLike($records[0]);
    }

    public function getLikeById(int $id): ?Like
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM likes WHERE id = ?";
        $records = $mysqli->prepareAndFetchAll($query, 'i', [$id]);
        return count($records) === 0 ? null : $this->convertRecordToLike($records[0]);
    }

    public function getLikeUsers(int $tweetId): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM likes WHERE tweet_id = ?";
        $records = $mysqli->prepareAndFetchAll($query, 'i', [$tweetId]);
        return is_null($records) ? [] : $this->convertRecordArrayToLikeArray($records);
    }

    public function delete(int $userId, int $tweetId): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM likes WHERE user_id = ? AND tweet_id = ?";
        return $mysqli->prepareAndExecute($sql, 'ii', [$userId, $tweetId]);
    }

    private function convertRecordArrayToLikeArray(array $records): array
    {
        $likes = [];
        foreach ($records as $record) {
            $like = $this->convertRecordToLike($record);
            array_push($likes, $like);
        }
        return $likes;
    }

    private function convertRecordToLike(array $data): Like
    {
        $like = new Like(
            id: $data['id'],
            userId: $data['user_id'],
            tweetId: $data['tweet_id'],
            likeDatetime: $data['like_datetime']
        );
        return $like;
    }
}
