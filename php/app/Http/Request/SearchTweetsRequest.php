<?php

namespace Http\Request;

class SearchTweetsRequest
{
    private string $query;
    private int $page;
    private int $limit;

    public function __construct(array $getData)
    {
        $this->query = $getData['query'];
        $this->page = $getData['page'];
        $this->limit = $getData['limit'];
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
