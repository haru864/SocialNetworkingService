<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\TweetsDAO;
use Database\DatabaseManager;
use Models\Tweet;
use Exceptions\InvalidDataException;
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
                media_file_path,
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
                $tweet->getMediaFilePath(),
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

    public function getByUserId(int $userId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM tweets WHERE user_id = ?";
        $records = $mysqli->prepareAndFetchAll($query, 'i', [$userId]) ?? null;
        return $records === null ? null : $this->convertRecordArrayToTweetArray($records);
    }

    public function update(Tweet $tweet): bool
    {
        if ($tweet->getId() === null) {
            throw new InvalidDataException('Tweet specified has no ID.');
        }
        $tweetsInTable = $this->getByUserId($tweet->getUserId());
        if ($tweetsInTable === null) {
            throw new InvalidDataException(sprintf("Tweet's user_id '%s' does not exist.", $tweet->getUserId()));
        }
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE
                tweets
            SET
                reply_to_id = ?,
                user_id = ?,
                message = ?,
                media_file_path = ?,
                media_type = ?,
                posting_datetime = ?
            WHERE
                id = ?
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'iissssi',
            [
                $tweet->getReplyToId(),
                $tweet->getUserId(),
                $tweet->getMessage(),
                $tweet->getMediaFilePath(),
                $tweet->getMediaType(),
                $tweet->getPostingDatetime(),
                $tweet->getId()
            ],
        );
        if (!$result) {
            throw new QueryFailedException("UPDATE 'tweets' failed.");
        }
        return $mysqli->insert_id;
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
            mediaFilePath: $data['media_file_path'],
            mediaType: $data['media_type'],
            postingDatetime: $data['posting_datetime']
        );
    }
}
