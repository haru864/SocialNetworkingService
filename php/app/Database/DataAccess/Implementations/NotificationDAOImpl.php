<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\NotificationDAO;
use Database\DatabaseManager;
use Database\DataTransfer\NotificationDTO;

class NotificationDAOImpl implements NotificationDAO
{
    public function getAllNotificationsSorted(int $userId, int $limit, int $offset): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT 'like' AS notification_type, id, notified_user_id, like_id AS entity_id, is_confirmed, created_at
            FROM like_notifications
            WHERE notified_user_id = ?
            UNION ALL
            SELECT 'follow' AS notification_type, id, notified_user_id, follow_id AS entity_id, is_confirmed, created_at
            FROM follow_notifications
            WHERE notified_user_id = ?
            UNION ALL
            SELECT 'message' AS notification_type, id, notified_user_id, message_id AS entity_id, is_confirmed, created_at
            FROM message_notifications
            WHERE notified_user_id = ?
            UNION ALL
            SELECT 'reply' AS notification_type, id, notified_user_id, reply_id AS entity_id, is_confirmed, created_at
            FROM reply_notifications
            WHERE notified_user_id = ?
            UNION ALL
            SELECT 'retweet' AS notification_type, id, notified_user_id, retweet_id AS entity_id, is_confirmed, created_at
            FROM retweet_notifications
            WHERE notified_user_id = ?
            ORDER BY created_at DESC
            LIMIT ?
            OFFSET ?
            ;
        SQL;
        $records = $mysqli->prepareAndFetchAll(
            $query,
            'iiiiiii',
            [$userId, $userId, $userId, $userId, $userId, $limit, $offset]
        ) ?? null;
        return $records === null ? null : $this->convertRecordArrayToNotificationDTOArray($records);
    }

    private function convertRecordArrayToNotificationDTOArray(array $records): array
    {
        $notifications = [];
        foreach ($records as $record) {
            $notification = $this->convertRecordToNotificationDTO($record);
            array_push($notifications, $notification->toArray());
        }
        return $notifications;
    }

    private function convertRecordToNotificationDTO(array $data): NotificationDTO
    {
        $notification = new NotificationDTO(
            notificationType: $data['notification_type'],
            id: $data['id'],
            notifiedUserId: $data['notified_user_id'],
            entityId: $data['entity_id'],
            isConfirmed: $data['is_confirmed'],
            createdAt: $data['created_at']
        );
        return $notification;
    }
}
