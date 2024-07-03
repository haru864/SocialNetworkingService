<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\ScheduledTweetsDAO;
use Database\DatabaseManager;
use Exceptions\QueryFailedException;
use Models\ScheduledTweet;

class ScheduledTweetsDAOImpl implements ScheduledTweetsDAO
{
    public function create(ScheduledTweet $scheduledTweet): ScheduledTweet
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO scheduled_tweets (
                reply_to_id,
                user_id,
                message,
                media_file_name,
                media_type,
                scheduled_datetime
            )
            VALUES (
                ?, ?, ?, ?, ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'iissss',
            [
                $scheduledTweet->getReplyToId(),
                $scheduledTweet->getUserId(),
                $scheduledTweet->getMessage(),
                $scheduledTweet->getMediaFileName(),
                $scheduledTweet->getMediaType(),
                $scheduledTweet->getScheduledDatetime()
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT failed.");
        }
        $scheduledTweet->setId($mysqli->insert_id);
        return $scheduledTweet;
    }

    public function getByScheduled(string $datetime): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT * FROM scheduled_tweets
            WHERE scheduled_datetime <= ?
        SQL;
        $record = $mysqli->prepareAndFetchAll($query, 's', [$datetime]);
        return $this->convertRecordArrayToScheduledTweetArray($record);
    }

    public function deleteById(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM scheduled_tweets WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }

    private function convertRecordArrayToScheduledTweetArray(array $records): array
    {
        $scheduledTweets = [];
        foreach ($records as $record) {
            $scheduledTweet = $this->convertRecordToScheduledTweet($record);
            array_push($scheduledTweets, $scheduledTweet);
        }
        return $scheduledTweets;
    }

    private function convertRecordToScheduledTweet(array $data): ScheduledTweet
    {
        return new ScheduledTweet(
            id: $data['id'],
            replyToId: $data['reply_to_id'],
            userId: $data['user_id'],
            message: $data['message'],
            mediaFileName: $data['media_file_name'],
            mediaType: $data['media_type'],
            scheduledDatetime: $data['scheduled_datetime']
        );
    }
}
