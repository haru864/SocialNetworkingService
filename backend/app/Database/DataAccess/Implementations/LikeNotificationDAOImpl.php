<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\LikeNotificationDAO;
use Database\DatabaseManager;
use Exceptions\QueryFailedException;
use Models\LikeNotification;

class LikeNotificationDAOImpl implements LikeNotificationDAO
{
    public function create(LikeNotification $likeNotification): LikeNotification
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO like_notifications (
                notified_user_id,
                like_id,
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
                $likeNotification->getNotifiedUserId(),
                $likeNotification->getLikeId(),
                $likeNotification->getIsConfirmed(),
                $likeNotification->getCreatedAt(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT INTO failed.");
        }
        $likeNotification->setId($mysqli->insert_id);
        return $likeNotification;
    }

    public function update(LikeNotification $likeNotification): LikeNotification
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE like_notifications
            SET
                notified_user_id = ?,
                like_id = ?,
                is_confirmed = ?,
                created_at = ?
            WHERE
                id = ?
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'iiisi',
            [
                $likeNotification->getNotifiedUserId(),
                $likeNotification->getLikeId(),
                $likeNotification->getIsConfirmed(),
                $likeNotification->getCreatedAt(),
                $likeNotification->getId(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("UPDATE failed.");
        }
        return $likeNotification;
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM like_notifications WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }
}
