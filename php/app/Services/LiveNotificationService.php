<?php

namespace Services;

use Models\Message;
use Settings\Settings;
use Database\DataAccess\Implementations\FollowNotificationDAOImpl;
use Database\DataAccess\Implementations\LikeNotificationDAOImpl;
use Database\DataAccess\Implementations\MessageNotificationDAOImpl;
use Database\DataAccess\Implementations\ReplyNotificationDAOImpl;
use Database\DataAccess\Implementations\RetweetNotificationDAOImpl;
use Database\DataAccess\Implementations\TweetsDAOImpl;
use Database\DataTransfer\NotificationDTO;
use Models\Follow;
use Models\FollowNotification;
use Models\Like;
use Models\LikeNotification;
use Models\MessageNotification;
use Models\ReplyNotification;
use Models\RetweetNotification;
use Models\Tweet;

class LiveNotificationService
{
    private LikeNotificationDAOImpl $likeNotificationDAOImpl;
    private FollowNotificationDAOImpl $followNotificationDAOImpl;
    private MessageNotificationDAOImpl $messageNotificationDAOImpl;
    private ReplyNotificationDAOImpl $replyNotificationDAOImpl;
    private RetweetNotificationDAOImpl $retweetNotificationDAOImpl;
    private TweetsDAOImpl $tweetsDAOImpl;

    public function __construct(
        LikeNotificationDAOImpl $likeNotificationDAOImpl,
        FollowNotificationDAOImpl $followNotificationDAOImpl,
        MessageNotificationDAOImpl $messageNotificationDAOImpl,
        ReplyNotificationDAOImpl $replyNotificationDAOImpl,
        RetweetNotificationDAOImpl $retweetNotificationDAOImpl,
        TweetsDAOImpl $tweetsDAOImpl
    ) {
        $this->likeNotificationDAOImpl = $likeNotificationDAOImpl;
        $this->followNotificationDAOImpl = $followNotificationDAOImpl;
        $this->messageNotificationDAOImpl = $messageNotificationDAOImpl;
        $this->replyNotificationDAOImpl = $replyNotificationDAOImpl;
        $this->retweetNotificationDAOImpl = $retweetNotificationDAOImpl;
        $this->tweetsDAOImpl = $tweetsDAOImpl;
    }

    private function postNotificationToSseEndpoint(NotificationDTO $notificationDTO)
    {
        $url = Settings::env('SSE_NOTIFICATION_URL');

        $data = $notificationDTO->toArray();
        $data_json = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_json)
        ));
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $errMsg = 'SSE Post Error: ' . curl_error($ch);
            throw new \Exception($errMsg);
        }

        curl_close($ch);
    }

    public function publishLikeNotification(Like $like): void
    {
        $likedTweetId = $like->getTweetId();
        $likedTweet = $this->tweetsDAOImpl->getByTweetId($likedTweetId);
        $tweetUserId = $likedTweet->getUserId();
        $likeNotification = new LikeNotification(
            id: null,
            notifiedUserId: $tweetUserId,
            likeId: $like->getId(),
            isConfirmed: false,
            createdAt: date('Y-m-d H:i:s')
        );
        $likeNotificationInTable = $this->likeNotificationDAOImpl->create($likeNotification);

        $notificationDTO = new NotificationDTO(
            notificationType: 'like',
            id: $likeNotificationInTable->getId(),
            notifiedUserId: $likeNotificationInTable->getNotifiedUserId(),
            entityId: $likeNotificationInTable->getLikeId(),
            isConfirmed: $likeNotificationInTable->getIsConfirmed(),
            createdAt: $likeNotificationInTable->getCreatedAt()
        );
        $this->postNotificationToSseEndpoint($notificationDTO);
    }

    public function publishReplyNotification(Tweet $replyTweet): void
    {
        $repliedTweetId = $replyTweet->getReplyToId();
        $repliedTweet = $this->tweetsDAOImpl->getByTweetId($repliedTweetId);
        $tweetUserId = $repliedTweet->getUserId();
        $replyNotification = new ReplyNotification(
            id: null,
            notifiedUserId: $tweetUserId,
            replyId: $replyTweet->getId(),
            isConfirmed: false,
            createdAt: date('Y-m-d H:i:s')
        );
        $replyNotificationInTable = $this->replyNotificationDAOImpl->create($replyNotification);

        $notificationDTO = new NotificationDTO(
            notificationType: 'reply',
            id: $replyNotificationInTable->getId(),
            notifiedUserId: $replyNotificationInTable->getNotifiedUserId(),
            entityId: $replyNotificationInTable->getReplyId(),
            isConfirmed: $replyNotificationInTable->getIsConfirmed(),
            createdAt: $replyNotificationInTable->getCreatedAt()
        );
        $this->postNotificationToSseEndpoint($notificationDTO);
    }

    public function publishRetweetNotification(Tweet $retweet): void
    {
        $retweetedTweetId = $retweet->getRetweetToId();
        $retweetedTweet = $this->tweetsDAOImpl->getByTweetId($retweetedTweetId);
        $tweetUserId = $retweetedTweet->getUserId();
        $retweetNotification = new RetweetNotification(
            id: null,
            notifiedUserId: $tweetUserId,
            retweetId: $retweet->getId(),
            isConfirmed: false,
            createdAt: date('Y-m-d H:i:s')
        );
        $retweetNotificationInTable = $this->retweetNotificationDAOImpl->create($retweetNotification);

        $notificationDTO = new NotificationDTO(
            notificationType: 'retweet',
            id: $retweetNotificationInTable->getId(),
            notifiedUserId: $retweetNotificationInTable->getNotifiedUserId(),
            entityId: $retweetNotificationInTable->getRetweetId(),
            isConfirmed: $retweetNotificationInTable->getIsConfirmed(),
            createdAt: $retweetNotificationInTable->getCreatedAt()
        );
        $this->postNotificationToSseEndpoint($notificationDTO);
    }

    public function publishFollowNotification(Follow $follow): void
    {
        $followeeId = $follow->getFolloweeId();
        $followNotification = new FollowNotification(
            id: null,
            notifiedUserId: $followeeId,
            followId: $follow->getId(),
            isConfirmed: false,
            createdAt: date('Y-m-d H:i:s')
        );
        $followNotificationInTable = $this->followNotificationDAOImpl->create($followNotification);

        $notificationDTO = new NotificationDTO(
            notificationType: 'follow',
            id: $followNotificationInTable->getId(),
            notifiedUserId: $followNotificationInTable->getNotifiedUserId(),
            entityId: $followNotificationInTable->getFollowId(),
            isConfirmed: $followNotificationInTable->getIsConfirmed(),
            createdAt: $followNotificationInTable->getCreatedAt()
        );
        $this->postNotificationToSseEndpoint($notificationDTO);
    }

    public function publishMessageNotification(Message $message): void
    {
        $recipientUserid  = $message->getRecipientId();
        $messageNotification = new MessageNotification(
            id: null,
            notifiedUserId: $recipientUserid,
            messageId: $message->getId(),
            isConfirmed: false,
            createdAt: date('Y-m-d H:i:s')
        );
        $messageNotificationInTable = $this->messageNotificationDAOImpl->create($messageNotification);

        $notificationDTO = new NotificationDTO(
            notificationType: 'message',
            id: $messageNotificationInTable->getId(),
            notifiedUserId: $messageNotificationInTable->getNotifiedUserId(),
            entityId: $messageNotificationInTable->getMessageId(),
            isConfirmed: $messageNotificationInTable->getIsConfirmed(),
            createdAt: $messageNotificationInTable->getCreatedAt()
        );
        $this->postNotificationToSseEndpoint($notificationDTO);
    }
}
