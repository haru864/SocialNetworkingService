<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Helpers\ValidationHelper;

class PostTweetRequest
{
    private string $message;
    private ?array $media;  // $_FILESオブジェクト
    private ?string $scheduledDatetime;

    public function __construct(array $postData, array $fileData)
    {
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
        $this->scheduledDatetime = $postData['dateTime'];
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getMedia(): ?array
    {
        return $this->media;
    }

    public function getScheduledDatetime(): ?string
    {
        return $this->scheduledDatetime;
    }
}
