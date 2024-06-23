<?php

namespace Services;

use Helpers\RedisManager;
use Helpers\SessionManager;
use Models\Message;
use Settings\Settings;
use React\EventLoop\Loop;
use Clue\React\Redis\Factory as RedisFactory;

class LiveMessageService
{
    private $redis;

    public function __construct()
    {
        $this->redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => Settings::env('REDIS_SERVER_ADDRESS'),
            'port'   => Settings::env('REDIS_SERVER_PORT'),
            'read_write_timeout' => -1
        ]);
    }

    // TODO 削除する
    // public function streamMessages(int $messagePartnerUserId): void
    // {
    //     $loginUserId = SessionManager::get('user_id');
    //     $channel = RedisManager::getMessageChannel($loginUserId, $messagePartnerUserId);

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

    private function postMessageToSseEndpoint(string $data_json)
    {
        $url = Settings::env('SSE_MESSAGE_URL');

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

    public function publishMessage(Message $message): void
    {
        // $channel = RedisManager::getMessageChannel($message->getSenderId(), $message->getRecipientId());
        $msg = json_encode([
            'id' => $message->getId(),
            'senderId' => $message->getSenderId(),
            'recipientId' => $message->getRecipientId(),
            'message' => $message->getMessage(),
            'mediaFileName' => $message->getMediaFileName(),
            'mediaType' => $message->getMediaType(),
            'sendDatetime' => $message->getSendDatetime()
        ]);
        // $this->redis->publish($channel, $msg);
        $this->postMessageToSseEndpoint($msg);
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
