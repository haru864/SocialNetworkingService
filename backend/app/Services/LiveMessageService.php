<?php

namespace Services;

use Helpers\RedisManager;
use Helpers\SessionManager;
use Models\Message;
use Settings\Settings;

class LiveMessageService
{
    private $redis;

    public function __construct(\Predis\Client $redis)
    {
        $this->redis = $redis;
    }

    public function streamMessages(int $messagePartnerUserId): void
    {
        $this->redis->connect('127.0.0.1', 6379);
        $loginUserId = SessionManager::get('user_id');
        $channel = RedisManager::getMessageChannel($loginUserId, $messagePartnerUserId);

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        $allowedOrigin = Settings::env('ACCESS_CONTROL_ALLOW_ORIGIN');
        $allowedMethods = 'GET, POST, DELETE';
        $allowedHeaders = 'Content-Type';
        header('Access-Control-Allow-Origin: ' . $allowedOrigin);
        header('Access-Control-Allow-Methods: ' . $allowedMethods);
        header('Access-Control-Allow-Headers: ' . $allowedHeaders);
        header('Access-Control-Allow-Credentials: true');
        header('X-Accel-Buffering: no');

        set_time_limit(0);

        /** @var \Consumer $pubsub */
        $pubsub = $this->redis->pubSubLoop();
        $pubsub->subscribe($channel);

        // BUG 一定時間メッセージが無いと内部エラーで落ちる
        foreach ($pubsub as $message) {

            // $logger = \Logging\Logger::getInstance();
            // $logger->logDebug('message: ' . json_encode($message));

            /** @var \Message $message */
            if ($message->kind === 'message') {
                echo "data: {$message->payload}\n\n";
                ob_end_flush();
                flush();
            }
            if (connection_aborted()) {
                $pubsub->unsubscribe();
                break;
            }
        }
    }

    public function publishMessage(Message $message): void
    {
        $this->redis->connect('127.0.0.1', 6379);
        $channel = RedisManager::getMessageChannel($message->getSenderId(), $message->getRecipientId());

        $msg = json_encode([
            'id' => $message->getId(),
            'senderId' => $message->getSenderId(),
            'recipientId' => $message->getRecipientId(),
            'message' => $message->getMessage(),
            'mediaFileName' => $message->getMediaFileName(),
            'mediaType' => $message->getMediaType(),
            'sendDatetime' => $message->getSendDatetime()
        ]);
        $this->redis->publish($channel, $msg);


        // $logger = \Logging\Logger::getInstance();
        // $logger->logDebug('publishMessages(): ' . $message);
    }
}
