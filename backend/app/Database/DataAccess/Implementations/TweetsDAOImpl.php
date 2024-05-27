<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\TweetsDAO;
use Database\DatabaseManager;
use Exceptions\InternalServerException;
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
                retweet_to_id,
                user_id,
                message,
                media_file_name,
                media_type,
                posting_datetime
            )
            VALUES (
                ?, ?, ?, ?, ?, ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'iiissss',
            [
                $tweet->getReplyToId(),
                $tweet->getRetweetToId(),
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

    public function getReplies(int $replyToId, int $limit = null, int $offset = null): ?array
    {
        if (
            (is_null($limit) && !is_null($offset))
            || (!is_null($limit) && is_null($offset))
        ) {
            throw new InternalServerException('limit and offset can be set both or neither.');
        }
        $mysqli = DatabaseManager::getMysqliConnection();
        if (is_null($limit) && is_null($offset)) {
            $query = <<<SQL
                SELECT * FROM tweets
                WHERE reply_to_id = ?
                ORDER BY id DESC
            SQL;
            $records = $mysqli->prepareAndFetchAll($query, 'i', [$replyToId]);
        } else {
            $query = <<<SQL
                SELECT * FROM tweets
                WHERE reply_to_id = ?
                ORDER BY id DESC
                LIMIT ?
                OFFSET ?
            SQL;
            $records = $mysqli->prepareAndFetchAll($query, 'iii', [$replyToId, $limit, $offset]);
        }
        return $records === null ? null : $this->convertRecordArrayToTweetArray($records);
    }

    public function getRetweets(int $tweetId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
                SELECT * FROM tweets
                WHERE retweet_to_id = ?
                ORDER BY id DESC
            SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'i', [$tweetId]);
        return $records === null ? null : $this->convertRecordArrayToTweetArray($records);
    }

    public function getRetweetByUser(int $userId, int $tweetId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
                SELECT * FROM tweets
                WHERE retweet_to_id = ? AND user_id = ?
                ORDER BY id DESC
            SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'ii', [$tweetId, $userId]);
        return $records === null ? null : $this->convertRecordArrayToTweetArray($records);
    }

    public function getByUserId(int $userId, int $limit, int $offset): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT * FROM tweets
            WHERE user_id = ?
            ORDER BY id DESC
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
            ORDER BY id DESC
            LIMIT ?
            OFFSET ?
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'iii', [$userId, $limit, $offset]);
        return $records === null ? null : $this->convertRecordArrayToTweetArray($records);
    }

    public function getByPopular(int $limit, int $offset): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT 
                id,
                reply_to_id,
                retweet_to_id,
                user_id,
                message,
                media_file_name,
                media_type,
                posting_datetime
            FROM
                trend_tweets_materialized_view
            LIMIT ?
            OFFSET ?
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'ii', [$limit, $offset]);
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
            retweetToId: $data['retweet_to_id'],
            userId: $data['user_id'],
            message: $data['message'],
            mediaFileName: $data['media_file_name'],
            mediaType: $data['media_type'],
            postingDatetime: $data['posting_datetime']
        );
    }
}
