<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Helpers\ValidationHelper;

class GetTweetsRequest
{
    private string $type;
    private int $page;
    private int $limit;

    public function __construct(array $getData)
    {
        $requiredParams = ['username', 'password', 'email'];
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
        $this->type = $getData['type'] ?? null;
        $this->page = $getData['page'] ?? null;
        $this->limit = $getData['limit'] ?? null;
    }

    public function getType(): string
    {
        return $this->type;
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
