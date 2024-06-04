<?php

namespace Http\Request;

class UpdateProfileRequest extends SignupRequest
{
    public function __construct($postData, $fileData)
    {
        parent::__construct($postData, $fileData);
    }
}
