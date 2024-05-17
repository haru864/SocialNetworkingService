<?php

namespace Services;

use Helpers\RedisManager;
use Helpers\SessionManager;
use Models\Message;
use Settings\Settings;

use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;

use React\EventLoop\Loop;
use Clue\React\Redis\Factory as RedisFactory;

class LiveMessageService
{
    private $redis;

    public function __construct(\Predis\Client $redis)
    {
        $this->redis = $redis;
    }

    public function streamMessages(int $messagePartnerUserId): void
    {
        $loginUserId = SessionManager::get('user_id');
        $channel = RedisManager::getMessageChannel($loginUserId, $messagePartnerUserId);

        $this->setHeader();
        set_time_limit(0);

        // /** @var \Consumer $pubsub */
        // $pubsub = $this->redis->pubSubLoop();
        // $pubsub->subscribe($channel);

        // foreach ($pubsub as $message) {

        //     $logger = \Logging\Logger::getInstance();
        //     $logger->logDebug(json_encode($message));

        //     /** @var \Message $message */
        //     if ($message->kind === 'message') {
        //         echo "data: {$message->payload}\n\n";
        //         ob_end_flush();
        //         flush();
        //     }
        //     if (connection_aborted()) {
        //         $pubsub->unsubscribe();
        //         break;
        //     }
        // }

        // -------------------------------------------

        $messages = [];

        $loop = Loop::get();

        $redisFactory = new RedisFactory($loop);
        $redis = $redisFactory->createLazyClient('redis://127.0.0.1:6379');

        $redis->subscribe($channel);

        $redis->on('message', function ($channel, $message) use (&$messages) {
            $messages[] = $message;
        });

        $last_heartbeat = time();

        $loop->addPeriodicTimer(0.5, function () use (&$last_heartbeat, &$messages) {
            $HEARTBEAT_PERIOD_SECONDS = 10;
            if (time() - $last_heartbeat >= $HEARTBEAT_PERIOD_SECONDS) {
                echo ": heartbeat\n\n";
                ob_flush();
                flush();
                $last_heartbeat = time();
            }
            while (!empty($messages)) {
                $message = array_shift($messages);

                $logger = \Logging\Logger::getInstance();
                $logger->logDebug(json_encode($message));

                echo "data: {$message}\n\n";
                ob_flush();
                flush();
            }
            if (connection_aborted()) {
                exit();
            }
        });
        $loop->run();
    }

    public function publishMessage(Message $message): void
    {
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
    }

    private function setHeader(): void
    {
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
    }
}
