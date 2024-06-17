<?php

namespace Helpers;

use Settings\Settings;

class SessionManager
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // session_set_cookie_params([
            //     'lifetime' => 0,
            //     'path' => '/',
            //     'domain' => Settings::env('SESSION_DOMAIN'),
            //     'secure' => filter_var(getenv('FEATURE_ENABLED'), FILTER_VALIDATE_BOOLEAN),
            //     'httponly' => true,
            //     'samesite' => 'None'
            // ]);
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
        // -----------------------------------------------
        if ($key == 'user_id') return 1;
        if ($key == 'user_name') return 'chisato';
        // -----------------------------------------------

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
