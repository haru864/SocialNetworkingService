<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Helpers\ValidationHelper;

class GetRepliesRequest
{
    private string $tweetId;
    private int $page;
    private int $limit;

    public function __construct(array $getData)
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->tweetId = explode('/', $uriDir)[3];
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

    public function getTweetId(): string
    {
        return $this->tweetId;
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
