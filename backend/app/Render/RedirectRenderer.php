<?php

namespace Render;

use Render\Interface\HTTPRenderer;
use Settings\Settings;

class RedirectRenderer implements HTTPRenderer
{
    private int $statusCode;
    private string $redirectUrl;

    public function __construct(int $statusCode, string $redirectUrl)
    {
        $this->statusCode = $statusCode;
        $this->redirectUrl = $redirectUrl;
    }

    public function isStringContent(): bool
    {
        return true;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

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
            'Access-Control-Allow-Credentials' => 'true',
            'Location' => $this->redirectUrl
        ];
    }

    public function getContent(): string
    {
        return '';
    }
}
