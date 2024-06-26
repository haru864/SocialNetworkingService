<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\FollowNotificationDAO;
use Database\DatabaseManager;
use Exceptions\QueryFailedException;
use Models\FollowNotification;

class FollowNotificationDAOImpl implements FollowNotificationDAO
{
    public function create(FollowNotification $followNotification): FollowNotification
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO follow_notifications (
                notified_user_id,
                follow_id,
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
                $followNotification->getNotifiedUserId(),
                $followNotification->getFollowId(),
                $followNotification->getIsConfirmed(),
                $followNotification->getCreatedAt(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT INTO failed.");
        }
        $followNotification->setId($mysqli->insert_id);
        return $followNotification;
    }

    public function update(FollowNotification $followNotification): FollowNotification
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE follow_notifications
            SET
                notified_user_id = ?,
                follow_id = ?,
                is_confirmed = ?,
                created_at = ?
            WHERE
                id = ?
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'iiisi',
            [
                $followNotification->getNotifiedUserId(),
                $followNotification->getFollowId(),
                $followNotification->getIsConfirmed(),
                $followNotification->getCreatedAt(),
                $followNotification->getId(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("UPDATE failed.");
        }
        return $followNotification;
    }

    public function confirmAllNotification(int $userId): void
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE follow_notifications
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
        $sql = "DELETE FROM follow_notifications WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }
}
