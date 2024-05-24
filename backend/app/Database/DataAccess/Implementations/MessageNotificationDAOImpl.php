<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\MessageNotificationDAO;
use Database\DatabaseManager;
use Exceptions\QueryFailedException;
use Models\MessageNotification;

class MessageNotificationDAOImpl implements MessageNotificationDAO
{
    public function create(MessageNotification $messageNotification): MessageNotification
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO message_notifications (
                notified_user_id,
                message_id,
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
                $messageNotification->getNotifiedUserId(),
                $messageNotification->getMessageId(),
                $messageNotification->getIsConfirmed(),
                $messageNotification->getCreatedAt(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT INTO failed.");
        }
        $messageNotification->setId($mysqli->insert_id);
        return $messageNotification;
    }

    public function update(MessageNotification $messageNotification): MessageNotification
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE message_notifications
            SET
                notified_user_id = ?,
                message_id = ?,
                is_confirmed = ?,
                created_at = ?
            WHERE
                id = ?
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'iiisi',
            [
                $messageNotification->getNotifiedUserId(),
                $messageNotification->getMessageId(),
                $messageNotification->getIsConfirmed(),
                $messageNotification->getCreatedAt(),
                $messageNotification->getId(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("UPDATE failed.");
        }
        return $messageNotification;
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM message_notifications WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }
}
