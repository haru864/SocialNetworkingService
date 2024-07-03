<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\RetweetNotificationDAO;
use Database\DatabaseManager;
use Exceptions\QueryFailedException;
use Models\RetweetNotification;

class RetweetNotificationDAOImpl implements RetweetNotificationDAO
{
    public function create(RetweetNotification $retweetNotification): RetweetNotification
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO retweet_notifications (
                notified_user_id,
                retweet_id,
                is_confirmed,
                created_at
            )
            VALUES (
                ?, ?, ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'iiis',
            [
                $retweetNotification->getNotifiedUserId(),
                $retweetNotification->getRetweetId(),
                $retweetNotification->getIsConfirmed(),
                $retweetNotification->getCreatedAt(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT INTO failed.");
        }
        $retweetNotification->setId($mysqli->insert_id);
        return $retweetNotification;
    }

    public function update(RetweetNotification $retweetNotification): RetweetNotification
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE retweet_notifications
            SET
                notified_user_id = ?,
                retweet_id = ?,
                is_confirmed = ?,
                created_at = ?
            WHERE
                id = ?
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'iiisi',
            [
                $retweetNotification->getNotifiedUserId(),
                $retweetNotification->getRetweetId(),
                $retweetNotification->getIsConfirmed(),
                $retweetNotification->getCreatedAt(),
                $retweetNotification->getId(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("UPDATE failed.");
        }
        return $retweetNotification;
    }

    public function confirmAllNotification(int $userId): void
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE retweet_notifications
            SET
                is_confirmed = 1
            WHERE
                notified_user_id = ? AND is_confirmed = 0
        SQL;
        $result = $mysqli->prepareAndExecute($query, 'i', [$userId],);
        if (!$result) {
            throw new QueryFailedException("CONFIRM failed.");
        }
        return;
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM retweet_notifications WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }
}
