<?php

namespace Helpers;

class RedisManager
{
    public static function getMessageChannel(int $loginUserId, int $messagePartnerUserId): string
    {
        $max = max($loginUserId, $messagePartnerUserId);
        $min = min($loginUserId, $messagePartnerUserId);
        return "chat:{$min}:{$max}";
    }
}
