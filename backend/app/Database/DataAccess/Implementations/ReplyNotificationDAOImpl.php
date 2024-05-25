<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\ReplyNotificationDAO;
use Database\DatabaseManager;
use Exceptions\QueryFailedException;
use Models\ReplyNotification;

class ReplyNotificationDAOImpl implements ReplyNotificationDAO
{
    public function create(ReplyNotification $replyNotification): ReplyNotification
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO reply_notifications (
                notified_user_id,
                reply_id,
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
                $replyNotification->getNotifiedUserId(),
                $replyNotification->getReplyId(),
                $replyNotification->getIsConfirmed(),
                $replyNotification->getCreatedAt(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT INTO failed.");
        }
        $replyNotification->setId($mysqli->insert_id);
        return $replyNotification;
    }

    public function update(ReplyNotification $replyNotification): ReplyNotification
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE reply_notifications
            SET
                notified_user_id = ?,
                reply_id = ?,
                is_confirmed = ?,
                created_at = ?
            WHERE
                id = ?
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'iiisi',
            [
                $replyNotification->getNotifiedUserId(),
                $replyNotification->getReplyId(),
                $replyNotification->getIsConfirmed(),
                $replyNotification->getCreatedAt(),
                $replyNotification->getId(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("UPDATE failed.");
        }
        return $replyNotification;
    }

    public function confirmAllNotification(int $userId): void
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE reply_notifications
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
        $sql = "DELETE FROM reply_notifications WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }
}
