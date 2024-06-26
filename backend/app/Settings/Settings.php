<?php

namespace Settings;

class Settings
{
    private const CONFIG_PATH =  __DIR__ . '/../../config/';
    private const PRIVATE_ENV_PATH =  self::CONFIG_PATH . '.env.private';
    private const PUBLIC_ENV_PATH =  self::CONFIG_PATH . '.env.public';

    public static function env(string $pair): string
    {
        $privateConfig = parse_ini_file(self::PRIVATE_ENV_PATH);
        $publicConfig = parse_ini_file(self::PUBLIC_ENV_PATH);
        if ($privateConfig === false) {
            throw new \Exception("ERROR: .env not found");
        }
        if ($publicConfig === false) {
            throw new \Exception("ERROR: .public.env not found");
        }
        $config = $privateConfig + $publicConfig;
        return $config[$pair];
    }
}
