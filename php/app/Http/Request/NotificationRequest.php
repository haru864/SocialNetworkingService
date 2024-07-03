<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Helpers\ValidationHelper;

class NotificationRequest
{
    private int $page;
    private int $limit;

    public function __construct(array $getData)
    {
        if (is_null($getData['page']) || is_null($getData['limit'])) {
            throw new InvalidRequestParameterException("'page' and 'limit' must be set at request.");
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

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }
}
