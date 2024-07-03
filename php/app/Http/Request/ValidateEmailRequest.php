<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;

class ValidateEmailRequest
{
    private string $id;

    public function __construct($getData)
    {
        if (is_null($getData['id'])) {
            throw new InvalidRequestParameterException("'id' must be set in query-string.");
        }
        $this->id = $getData['id'];
    }

    public function getId(): string
    {
        return $this->id;
    }
}
