<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Helpers\ValidationHelper;

class PostReplyRequest
{
    private string $tweetId;
    private string $message;
    private ?array $media;  // $_FILESオブジェクト

    public function __construct(array $postData, array $fileData)
    {
        $uriDir = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->tweetId = explode('/', $uriDir)[3];
        if (!ValidationHelper::isPositiveIntegerString($this->tweetId)) {
            throw new InvalidRequestParameterException("'tweet_id' must be positive integer.");
        }
        if (is_null($postData['message'])) {
            throw new InvalidRequestParameterException("'message' must be set in post-tweet request.");
        }
        $media = null;
        if (isset($fileData['media']) && $fileData['media']['tmp_name'] !== '') {
            $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $fileInfo->file($fileData['media']['tmp_name']);
            if (strpos($mimeType, 'image/') === 0) {
                ValidationHelper::validateUploadedImage('media');
            } else if (strpos($mimeType, 'video/') === 0) {
                ValidationHelper::validateUploadedVideo('media');
            } else {
                throw new InvalidRequestParameterException("'media' must be image or video file.");
            }
            $media = $fileData['media'];
        }
        $this->message = $postData['message'];
        $this->media = $media;
    }

    public function getTweetId(): string
    {
        return $this->tweetId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getMedia(): ?array
    {
        return $this->media;
    }
}
