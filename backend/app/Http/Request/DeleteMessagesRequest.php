<?php

namespace Http\Request;

use Exceptions\InvalidRequestURIException;

class DeleteMessagesRequest
{
    private ?int $recipientUserId;

    public function __construct()
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $ltrimedUriDir = ltrim($uriDir, "/");
        $uriDirArr = explode('/', $ltrimedUriDir);
        if (count($uriDirArr) !== 3) {
            throw new InvalidRequestURIException("Invalid request uri.");
        }
        $this->recipientUserId = $uriDirArr[2];
    }

    public function getRecipientUserId(): ?int
    {
        return $this->recipientUserId;
    }
}
