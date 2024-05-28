<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;

class SearchUsersRequest
{
    private string $query;
    private string $field;
    private int $page;
    private int $limit;

    public function __construct(array $getData)
    {
        $this->query = $getData['query'];
        $this->field = $getData['field'];
        $this->page = $getData['page'];
        $this->limit = $getData['limit'];

        $validFields = ['name', 'address', 'job'];
        if (!in_array($this->field, $validFields)) {
            $validFieldsString = implode(",", $validFields);
            throw new InvalidRequestParameterException("Parameter 'field' must be follow. [{$validFieldsString}]");
        }
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getField(): string
    {
        return $this->field;
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
