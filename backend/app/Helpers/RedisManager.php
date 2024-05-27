<?php

namespace Helpers;

use Settings\Settings;

class RedisManager
{
    private static ?\Predis\Client $redisClient = null;

    public static function getInstance(): \Predis\Client
    {
        if (is_null(self::$redisClient)) {
            self::$redisClient = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => Settings::env('REDIS_SERVER_ADDRESS'),
                'port'   => Settings::env('REDIS_SERVER_PORT'),
            ]);
        }
        return self::$redisClient;
    }

    /**
     * @param int $loginUserId ログインユーザーのID
     * @param int $messagePartnerUserId メッセージ相手のユーザーID
     * @return string チャンネル名
     */
    public static function getMessageChannel(int $loginUserId, int $messagePartnerUserId): string
    {
        $max = max($loginUserId, $messagePartnerUserId);
        $min = min($loginUserId, $messagePartnerUserId);
        return "chat:{$min}:{$max}";
    }

    /**
     * @param int $notifiedUserId 通知を受け取るユーザーのID
     * @return string チャンネル名
     */
    public static function getNotificationChannel(int $notifiedUserId): string
    {
        return "notification:{$notifiedUserId}";
    }

    /**
     * @param int $loginUserId ログインユーザーのID
     * @return string チャンネル名
     */
    public static function getSessionCheckChannel(int $loginUserId): string
    {
        return "session:{$loginUserId}";
    }
}
