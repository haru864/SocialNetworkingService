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
        $requiredParams = ['type', 'page', 'limit'];
        foreach ($requiredParams as $param) {
            if (is_null($getData[$param])) {
                throw new InvalidRequestParameterException("'{$param}' must be set in get-tweets request.");
            }
        }
        $validTypes = ['popular', 'followers', 'user'];
        if (!in_array($getData['type'], $validTypes)) {
            $validTypeStr = implode(",", $validTypes);
            throw new InvalidRequestParameterException("'type' must be in [{$validTypeStr}].");
        }
        if (
            !ValidationHelper::isPositiveIntegerString($getData['page'])
            || !ValidationHelper::isPositiveIntegerString($getData['limit'])
        ) {
            throw new InvalidRequestParameterException("'page' and 'limit' must be positive integer string.");
        }
        $this->type = $getData['type'];
        $this->page = $getData['page'];
        $this->limit = $getData['limit'];
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
