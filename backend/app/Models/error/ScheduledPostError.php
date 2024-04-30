<?php

namespace Models\error;


class ScheduledPostError
{
    private array $postInfo;
    private string $errorMessage;

    public function __construct($postInfo, $errorMessage)
    {
        $this->postInfo = $postInfo;
        $this->errorMessage = $errorMessage;
    }

    public function getPostInfo(): array
    {
        return $this->postInfo;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
