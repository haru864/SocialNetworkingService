<?php

namespace Services;

use Database\DataAccess\Implementations\FollowNotificationDAOImpl;
use Database\DataAccess\Implementations\LikeNotificationDAOImpl;
use Database\DataAccess\Implementations\MessageNotificationDAOImpl;
use Database\DataAccess\Implementations\NotificationDAOImpl;
use Database\DataAccess\Implementations\ReplyNotificationDAOImpl;
use Database\DataAccess\Implementations\RetweetNotificationDAOImpl;

class NotificationService
{
    private NotificationDAOImpl $notificationDAOImpl;
    private LikeNotificationDAOImpl $likeNotificationDAOImpl;
    private FollowNotificationDAOImpl $followNotificationDAOImpl;
    private MessageNotificationDAOImpl $messageNotificationDAOImpl;
    private ReplyNotificationDAOImpl $replyNotificationDAOImpl;
    private RetweetNotificationDAOImpl $retweetNotificationDAOImpl;

    public function __construct(
        NotificationDAOImpl $notificationDAOImpl,
        LikeNotificationDAOImpl $likeNotificationDAOImpl,
        FollowNotificationDAOImpl $followNotificationDAOImpl,
        MessageNotificationDAOImpl $messageNotificationDAOImpl,
        ReplyNotificationDAOImpl $replyNotificationDAOImpl,
        RetweetNotificationDAOImpl $retweetNotificationDAOImpl
    ) {
        $this->notificationDAOImpl = $notificationDAOImpl;
        $this->likeNotificationDAOImpl = $likeNotificationDAOImpl;
        $this->followNotificationDAOImpl = $followNotificationDAOImpl;
        $this->messageNotificationDAOImpl = $messageNotificationDAOImpl;
        $this->replyNotificationDAOImpl = $replyNotificationDAOImpl;
        $this->retweetNotificationDAOImpl = $retweetNotificationDAOImpl;
    }

    public function getAllNotificationsSorted(int $userId, int $limit, int $offset): array
    {
        $notifications = $this->notificationDAOImpl->getAllNotificationsSorted($userId, $limit, $offset);
        if (is_null($notifications)) {
            return [];
        }
        return $notifications;
    }

    public function confirmAllNotifications(int $loginUserId): void
    {
        $this->confirmAllLikeNotification($loginUserId);
        $this->confirmAllFollowNotification($loginUserId);
        $this->confirmAllMessageNotification($loginUserId);
        $this->confirmAllReplyNotification($loginUserId);
        $this->confirmAllRetweetNotification($loginUserId);
    }

    private function confirmAllLikeNotification(int $userId): void
    {
        $this->likeNotificationDAOImpl->confirmAllNotification($userId);
    }

    private function confirmAllFollowNotification(int $userId): void
    {
        $this->followNotificationDAOImpl->confirmAllNotification($userId);
    }

    private function confirmAllMessageNotification(int $userId): void
    {
        $this->messageNotificationDAOImpl->confirmAllNotification($userId);
    }

    private function confirmAllReplyNotification(int $userId): void
    {
        $this->replyNotificationDAOImpl->confirmAllNotification($userId);
    }

    private function confirmAllRetweetNotification(int $userId): void
    {
        $this->retweetNotificationDAOImpl->confirmAllNotification($userId);
    }
}
