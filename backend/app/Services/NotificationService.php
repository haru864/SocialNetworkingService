<?php

namespace Services;

use Database\DataAccess\Implementations\FollowNotificationDAOImpl;
use Database\DataAccess\Implementations\LikeNotificationDAOImpl;
use Database\DataAccess\Implementations\MessageNotificationDAOImpl;
use Database\DataAccess\Implementations\NotificationDAOImpl;
use Database\DataAccess\Implementations\ReplyNotificationDAOImpl;
use Database\DataAccess\Implementations\RetweetNotificationDAOImpl;
use Models\FollowNotification;
use Models\LikeNotification;
use Models\MessageNotification;
use Models\ReplyNotification;
use Models\RetweetNotification;

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
        foreach ($notifications as $notification) {
            if ($notification['isConfirmed']) {
                continue;
            }
            switch ($notification['notificationType']) {
                case 'like':
                    $likeNotificationConfirmed = new LikeNotification(
                        id: $notification['id'],
                        notifiedUserId: $notification['notifiedUserId'],
                        likeId: $notification['entityId'],
                        isConfirmed: true,
                        createdAt: $notification['createdAt']
                    );
                    $this->likeNotificationDAOImpl->update($likeNotificationConfirmed);
                    break;
                case 'follow':
                    $followNotificationConfirmed = new FollowNotification(
                        id: $notification['id'],
                        notifiedUserId: $notification['notifiedUserId'],
                        followId: $notification['entityId'],
                        isConfirmed: true,
                        createdAt: $notification['createdAt']
                    );
                    $this->followNotificationDAOImpl->update($followNotificationConfirmed);
                    break;
                case 'message':
                    $messageNotificationConfirmed = new MessageNotification(
                        id: $notification['id'],
                        notifiedUserId: $notification['notifiedUserId'],
                        messageId: $notification['entityId'],
                        isConfirmed: true,
                        createdAt: $notification['createdAt']
                    );
                    $this->messageNotificationDAOImpl->update($messageNotificationConfirmed);
                    break;
                case 'reply':
                    $replyNotificationConfirmed = new ReplyNotification(
                        id: $notification['id'],
                        notifiedUserId: $notification['notifiedUserId'],
                        replyId: $notification['entityId'],
                        isConfirmed: true,
                        createdAt: $notification['createdAt']
                    );
                    $this->replyNotificationDAOImpl->update($replyNotificationConfirmed);
                    break;
                case 'retweet':
                    $retweetNotificationConfirmed = new RetweetNotification(
                        id: $notification['id'],
                        notifiedUserId: $notification['notifiedUserId'],
                        retweetId: $notification['entityId'],
                        isConfirmed: true,
                        createdAt: $notification['createdAt']
                    );
                    $this->retweetNotificationDAOImpl->update($retweetNotificationConfirmed);
                    break;
                default:
                    break;
            }
        }
        return $notifications;
    }
}
