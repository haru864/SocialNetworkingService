<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\RetweetsDAO;
use Database\DatabaseManager;
use Models\Retweet;
use Exceptions\QueryFailedException;

class RetweetsDAOImpl implements RetweetsDAO
{
    public function create(Retweet $retweet): Retweet
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO retweets (
                user_id,
                tweet_id,
                message,
                retweet_datetime
            )
            VALUES (
                ?, ?, ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'iiss',
            [
                $retweet->getUserId(),
                $retweet->getTweetId(),
                $retweet->getMessage(),
                $retweet->getRetweetDatetime()
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT failed.");
        }
        $retweet->setId($mysqli->insert_id);
        return $retweet;
    }

    public function getRetweet(int $userId, int $tweetId): ?Retweet
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT * FROM retweets
            WHERE user_id = ? AND tweet_id = ?
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'ii', [$userId, $tweetId]);
        if (is_null($records) || count($records) === 0) {
            return null;
        }
        return $this->convertRecordToRetweet($records[0]);
    }

    public function getByTweetId(int $tweetId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT * FROM retweets
            WHERE tweet_id = ?
            ORDER BY retweet_datetime DESC
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'i', [$tweetId]);
        return $records === null ? null : $this->convertRecordArrayToRetweetArray($records);
    }

    public function deleteById(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM retweets WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }

    private function convertRecordArrayToRetweetArray(array $records): array
    {
        $retweets = [];
        foreach ($records as $record) {
            $retweet = $this->convertRecordToRetweet($record);
            array_push($retweets, $retweet);
        }
        return $retweets;
    }

    private function convertRecordToRetweet(array $data): Retweet
    {
        return new Retweet(
            id: $data['id'],
            userId: $data['user_id'],
            tweetId: $data['tweet_id'],
            message: $data['message'],
            retweetDatetime: $data['retweet_datetime']
        );
    }
}
