<?php

namespace Services;

use Database\DataAccess\Implementations\NotificationDAOImpl;

class NotificationService
{
    private NotificationDAOImpl $notificationDAOImpl;

    public function __construct(NotificationDAOImpl $notificationDAOImpl)
    {
        $this->notificationDAOImpl = $notificationDAOImpl;
    }

    public function getAllNotificationsSorted(int $userId, int $limit, int $offset): array
    {
        $notifications = $this->notificationDAOImpl->getAllNotificationsSorted($userId, $limit, $offset);
        if (is_null($notifications)) {
            return [];
        }
        return $notifications;
    }
}
