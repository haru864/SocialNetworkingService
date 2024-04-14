<?php

namespace Render;

use Render\Interface\HTTPRenderer;
use Settings\Settings;

class JSONRenderer implements HTTPRenderer
{
    private int $statusCode;
    private array $data;

    public function __construct(int $statusCode, array $data)
    {
        $this->statusCode = $statusCode;
        $this->data = $data;
    }

    public function isStringContent(): bool
    {
        return true;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    // TODO Pre-Flight以外でもAccess-Controlヘッダーを返して良いか調べる
    public function getFields(): array
    {
        $allowedOrigin = Settings::env('ACCESS_CONTROL_ALLOW_ORIGIN');
        $allowedMethods = 'GET, POST, DELETE';
        $allowedHeaders = 'Content-Type';
        return [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Access-Control-Allow-Origin' => $allowedOrigin,
            'Access-Control-Allow-Methods' => $allowedMethods,
            'Access-Control-Allow-Headers' => $allowedHeaders,
        ];
    }

    public function getContent(): string
    {
        return json_encode($this->data, JSON_THROW_ON_ERROR);
    }
}
