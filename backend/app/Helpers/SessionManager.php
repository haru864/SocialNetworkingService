<?php

namespace Helpers;

class SessionManager
{
    public static function startSession(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value): void
    {
        self::startSession();
        $_SESSION[$key] = $value;
    }

    public static function get($key): mixed
    {
        self::startSession();

        // TODO ITテスト後に削除する
        // if ($key == 'user_id') return 14;
        // if ($key == 'user_name') return 'takina';

        return $_SESSION[$key] ?? null;
    }

    public static function destroySession(): void
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }
}
