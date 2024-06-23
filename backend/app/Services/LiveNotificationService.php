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
use Database\DataTransfer\NotificationDTO;
use Exception;
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
    private \Predis\Client $redis;
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
        $this->redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => Settings::env('REDIS_SERVER_ADDRESS'),
            'port'   => Settings::env('REDIS_SERVER_PORT'),
            'read_write_timeout' => -1
        ]);
        $this->likeNotificationDAOImpl = $likeNotificationDAOImpl;
        $this->followNotificationDAOImpl = $followNotificationDAOImpl;
        $this->messageNotificationDAOImpl = $messageNotificationDAOImpl;
        $this->replyNotificationDAOImpl = $replyNotificationDAOImpl;
        $this->retweetNotificationDAOImpl = $retweetNotificationDAOImpl;
        $this->tweetsDAOImpl = $tweetsDAOImpl;
    }

    // TODO 削除する
    // public function streamNotification(): void
    // {
    //     $loginUserId = SessionManager::get('user_id');
    //     $channel = RedisManager::getNotificationChannel($loginUserId);

    //     $this->setHeader();
    //     set_time_limit(0);

    //     $messages = [];

    //     $loop = Loop::get();

    //     $redisFactory = new RedisFactory($loop);
    //     $redis = $redisFactory->createLazyClient('redis://' . Settings::env('REDIS_SERVER_ADDRESS') . ':' . Settings::env('REDIS_SERVER_PORT'));

    //     $redis->subscribe($channel);

    //     $redis->on('message', function ($channel, $message) use (&$messages) {
    //         $messages[] = $message;
    //     });

    //     $last_heartbeat = time();

    //     $loop->addPeriodicTimer(0.5, function () use (&$last_heartbeat, &$messages) {
    //         $HEARTBEAT_PERIOD_SECONDS = 10;
    //         if (time() - $last_heartbeat >= $HEARTBEAT_PERIOD_SECONDS) {
    //             echo ": heartbeat\n\n";
    //             ob_flush();
    //             flush();
    //             $last_heartbeat = time();
    //         }
    //         while (!empty($messages)) {
    //             $message = array_shift($messages);
    //             echo "data: {$message}\n\n";
    //             ob_flush();
    //             flush();
    //         }
    //         if (connection_aborted()) {
    //             exit();
    //         }
    //     });
    //     $loop->run();
    // }

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

        // $channel = RedisManager::getNotificationChannel($tweetUserId);
        $notificationDTO = new NotificationDTO(
            notificationType: 'like',
            id: $likeNotificationInTable->getId(),
            notifiedUserId: $likeNotificationInTable->getNotifiedUserId(),
            entityId: $likeNotificationInTable->getLikeId(),
            isConfirmed: $likeNotificationInTable->getIsConfirmed(),
            createdAt: $likeNotificationInTable->getCreatedAt()
        );
        // $this->redis->publish($channel, json_encode($notificationDTO->toArray()));
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

        // $channel = RedisManager::getNotificationChannel($tweetUserId);
        $notificationDTO = new NotificationDTO(
            notificationType: 'reply',
            id: $replyNotificationInTable->getId(),
            notifiedUserId: $replyNotificationInTable->getNotifiedUserId(),
            entityId: $replyNotificationInTable->getReplyId(),
            isConfirmed: $replyNotificationInTable->getIsConfirmed(),
            createdAt: $replyNotificationInTable->getCreatedAt()
        );
        // $this->redis->publish($channel, json_encode($notificationDTO->toArray()));
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

        // $channel = RedisManager::getNotificationChannel($tweetUserId);
        $notificationDTO = new NotificationDTO(
            notificationType: 'retweet',
            id: $retweetNotificationInTable->getId(),
            notifiedUserId: $retweetNotificationInTable->getNotifiedUserId(),
            entityId: $retweetNotificationInTable->getRetweetId(),
            isConfirmed: $retweetNotificationInTable->getIsConfirmed(),
            createdAt: $retweetNotificationInTable->getCreatedAt()
        );
        // $this->redis->publish($channel, json_encode($notificationDTO->toArray()));
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

        // $channel = RedisManager::getNotificationChannel($followeeId);
        $notificationDTO = new NotificationDTO(
            notificationType: 'follow',
            id: $followNotificationInTable->getId(),
            notifiedUserId: $followNotificationInTable->getNotifiedUserId(),
            entityId: $followNotificationInTable->getFollowId(),
            isConfirmed: $followNotificationInTable->getIsConfirmed(),
            createdAt: $followNotificationInTable->getCreatedAt()
        );
        // $this->redis->publish($channel, json_encode($notificationDTO->toArray()));
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

        // $channel = RedisManager::getNotificationChannel($recipientUserid);
        $notificationDTO = new NotificationDTO(
            notificationType: 'message',
            id: $messageNotificationInTable->getId(),
            notifiedUserId: $messageNotificationInTable->getNotifiedUserId(),
            entityId: $messageNotificationInTable->getMessageId(),
            isConfirmed: $messageNotificationInTable->getIsConfirmed(),
            createdAt: $messageNotificationInTable->getCreatedAt()
        );
        // $this->redis->publish($channel, json_encode($notificationDTO->toArray()));
        $this->postNotificationToSseEndpoint($notificationDTO);
    }

    // TODO 削除する
    // private function setHeader(): void
    // {
    //     header('Content-Type: text/event-stream');
    //     header('Cache-Control: no-cache');
    //     header('Connection: keep-alive');
    //     $allowedOrigin = Settings::env('ACCESS_CONTROL_ALLOW_ORIGIN');
    //     $allowedMethods = 'GET, POST, DELETE';
    //     $allowedHeaders = 'Content-Type';
    //     header('Access-Control-Allow-Origin: ' . $allowedOrigin);
    //     header('Access-Control-Allow-Methods: ' . $allowedMethods);
    //     header('Access-Control-Allow-Headers: ' . $allowedHeaders);
    //     header('Access-Control-Allow-Credentials: true');
    //     header('X-Accel-Buffering: no');
    // }
}
