<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;

class PostProfileRequest extends SignupRequest
{
    private string $action;

    public function __construct($postData, $fileData)
    {
        if ($postData['action'] !== 'edit' && $postData['action'] !== 'delete') {
            throw new InvalidRequestParameterException("'action' must be 'edit' or 'delete'.");
        }
        $this->action = $postData['action'];
        if ($this->action === 'delete') {
            return;
        }
        parent::__construct($postData, $fileData);
    }

    public function getAction(): string
    {
        return $this->action;
    }
}
