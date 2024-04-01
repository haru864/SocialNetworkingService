<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Helpers\ValidationHelper;

class PostTweetRequest
{
    private string $message;
    private string $media;

    public function __construct(array $postData)
    {
        $this->message = $postData['message'] ?? null;
        $this->media = $postData['media'] ?? null;
        if (is_null($this->message)) {
            throw new InvalidRequestParameterException("'message' must be set in post-tweet request.");
        }
        if (isset($this->media)) {
            ValidationHelper::validateUploadedVideo('media');
        }
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getMedia(): string
    {
        return $this->media;
    }
}
