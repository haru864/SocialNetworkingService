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

    public function getAllNotificationsSorted(int $userId): array
    {
        $notifications = $this->notificationDAOImpl->getAllNotificationsSorted($userId);
        if (is_null($notifications)) {
            return [];
        }
        return $notifications;
    }
}
