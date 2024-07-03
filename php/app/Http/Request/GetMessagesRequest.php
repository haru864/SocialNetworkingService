<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Exceptions\InvalidRequestURIException;
use Helpers\ValidationHelper;

class GetMessagesRequest
{
    private ?int $recipientUserId;
    private int $page;
    private int $limit;

    public function __construct(array $getData)
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $ltrimedUriDir = ltrim($uriDir, "/");
        $uriDirArr = explode('/', $ltrimedUriDir);
        if (count($uriDirArr) == 2) {
            $this->recipientUserId = null;
        } else if (count($uriDirArr) == 3) {
            $this->recipientUserId = $uriDirArr[2];
        } else {
            throw new InvalidRequestURIException("Invalid request uri.");
        }

        $requiredParams = ['page', 'limit'];
        foreach ($requiredParams as $param) {
            if (is_null($getData[$param])) {
                throw new InvalidRequestParameterException("'{$param}' must be set in get-tweets request.");
            }
        }
        if (
            !ValidationHelper::isPositiveIntegerString($getData['page'])
            || !ValidationHelper::isPositiveIntegerString($getData['limit'])
        ) {
            throw new InvalidRequestParameterException("'page' and 'limit' must be positive integer string.");
        }
        $this->page = $getData['page'];
        $this->limit = $getData['limit'];
    }

    public function getRecipientUserId(): ?int
    {
        return $this->recipientUserId;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): string
    {
        return $this->limit;
    }
}
