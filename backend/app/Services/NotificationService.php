<?php

namespace Services;

use Helpers\RedisManager;
use Helpers\SessionManager;
use Models\Message;
use Settings\Settings;
use React\EventLoop\Loop;
use Clue\React\Redis\Factory as RedisFactory;
use Database\DataAccess\Implementations\FollowNotificationDAOImpl;
use Database\DataAccess\Implementations\LikeNotificationDAOImpl;
use Database\DataAccess\Implementations\MessageNotificationDAOImpl;
use Database\DataAccess\Implementations\ReplyNotificationDAOImpl;
use Database\DataAccess\Implementations\RetweetNotificationDAOImpl;
use Database\DataAccess\Implementations\TweetsDAOImpl;
use Models\Follow;
use Models\FollowNotification;
use Models\Like;
use Models\LikeNotification;
use Models\MessageNotification;
use Models\ReplyNotification;
use Models\RetweetNotification;
use Models\Tweet;

class NotificationService
{
    private LikeNotificationDAOImpl $likeNotificationImpl;
    private FollowNotificationDAOImpl $followNotificationImpl;
    private MessageNotificationDAOImpl $messageNotificationImpl;
    private ReplyNotificationDAOImpl $replyNotificationImpl;
    private RetweetNotificationDAOImpl $retweetNotificationImpl;
    private TweetsDAOImpl $tweetsDAOImpl;

    public function __construct(
        LikeNotificationDAOImpl $likeNotificationImpl,
        FollowNotificationDAOImpl $followNotificationImpl,
        MessageNotificationDAOImpl $messageNotificationImpl,
        ReplyNotificationDAOImpl $replyNotificationImpl,
        RetweetNotificationDAOImpl $retweetNotificationImpl,
        TweetsDAOImpl $tweetsDAOImpl
    ) {
        $$this->likeNotificationImpl = $likeNotificationImpl;
        $$this->followNotificationImpl = $followNotificationImpl;
        $$this->messageNotificationImpl = $messageNotificationImpl;
        $$this->replyNotificationImpl = $replyNotificationImpl;
        $$this->retweetNotificationImpl = $retweetNotificationImpl;
        $$this->tweetsDAOImpl = $tweetsDAOImpl;
    }

    public function getAllNotificationsSorted(): array
    {
        
    }
}
