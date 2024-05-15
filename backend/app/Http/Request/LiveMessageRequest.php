<?php

namespace Http\Request;

use Exceptions\InvalidRequestURIException;

class LiveMessageRequest
{
    private int $messagePartnerId;

    public function __construct()
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $ltrimedUriDir = ltrim($uriDir, "/");
        $uriDirArr = explode('/', $ltrimedUriDir);
        $urlDirCount = 4;
        if (count($uriDirArr) !== $urlDirCount) {
            throw new InvalidRequestURIException("Invalid request uri.");
        }
        $this->messagePartnerId = $uriDirArr[$urlDirCount - 1];
    }

    public function getMessagePartnerId(): int
    {
        return $this->messagePartnerId;
    }
}
