<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\TweetsDAO;
use Database\DatabaseManager;
use Models\Tweet;
use Exceptions\QueryFailedException;

class TweetsDAOImpl implements TweetsDAO
{
    public function create(Tweet $tweet): Tweet
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO tweets (
                reply_to_id,
                user_id,
                message,
                media_file_name,
                media_type,
                posting_datetime
            )
            VALUES (
                ?, ?, ?, ?, ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'iissss',
            [
                $tweet->getReplyToId(),
                $tweet->getUserId(),
                $tweet->getMessage(),
                $tweet->getMediaFileName(),
                $tweet->getMediaType(),
                $tweet->getPostingDatetime()
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT INTO 'tweets' failed.");
        }
        $tweet->setId($mysqli->insert_id);
        return $tweet;
    }

    public function getByTweetId(int $id): ?Tweet
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT * FROM tweets
            WHERE id = ?
        SQL;
        $record = $mysqli->prepareAndFetchAll($query, 'i', [$id])[0];
        return $record === null ? null : $this->convertRecordToTweet($record);
    }

    public function getByReplyToId(int $replyToId, int $limit, int $offset): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT * FROM tweets
            WHERE reply_to_id = ?
            ORDER BY posting_datetime DESC
            LIMIT ?
            OFFSET ?
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'iii', [$replyToId, $limit, $offset]);
        return $records === null ? null : $this->convertRecordArrayToTweetArray($records);
    }

    public function getByUserId(int $userId, int $limit, int $offset): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT * FROM tweets
            WHERE user_id = ?
            ORDER BY posting_datetime DESC
            LIMIT ?
            OFFSET ?
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'iii', [$userId, $limit, $offset]);
        return $records === null ? null : $this->convertRecordArrayToTweetArray($records);
    }

    public function getByFollower(int $userId, int $limit, int $offset): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT * FROM tweets
            WHERE user_id IN (SELECT follower_id FROM follows WHERE followee_id = ?)
            ORDER BY posting_datetime DESC
            LIMIT ?
            OFFSET ?
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'iii', [$userId, $limit, $offset]);
        return $records === null ? null : $this->convertRecordArrayToTweetArray($records);
    }

    public function deleteById(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM tweets WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }

    private function convertRecordArrayToTweetArray(array $records): array
    {
        $tweets = [];
        foreach ($records as $record) {
            $tweet = $this->convertRecordToTweet($record);
            array_push($tweets, $tweet);
        }
        return $tweets;
    }

    private function convertRecordToTweet(array $data): Tweet
    {
        return new Tweet(
            id: $data['id'],
            replyToId: $data['reply_to_id'],
            userId: $data['user_id'],
            message: $data['message'],
            mediaFileName: $data['media_file_name'],
            mediaType: $data['media_type'],
            postingDatetime: $data['posting_datetime']
        );
    }
}
