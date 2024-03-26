<?php

namespace Validate;

use Settings\Settings;
use Exceptions\FileSizeLimitExceededException;
use Exceptions\InvalidTextException;
use Exceptions\InvalidMimeTypeException;
use Exceptions\InvalidRequestMethodException;
use Exceptions\InvalidRequestParameterException;
use Exceptions\InvalidContentTypeException;

class ValidationHelper
{
    private static function validateImage(): void
    {
        $ALLOWED_MIME_TYPE = ['image/jpeg', 'image/png', 'image/gif'];
        $REQUEST_PARAM_NAME = 'image';

        if ($_FILES[$REQUEST_PARAM_NAME]['error'] != UPLOAD_ERR_OK) {
            throw new InvalidRequestParameterException("Upload Error: error occured when uploading file.");
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $imagePath = $_FILES[$REQUEST_PARAM_NAME]['tmp_name'];
        $mimeType = $finfo->file($imagePath);
        if (!in_array($mimeType, $ALLOWED_MIME_TYPE)) {
            throw new InvalidMimeTypeException("Invalid Mime Type: jpeg, png, gif are allowed. Given MIME-TYPE was '{$mimeType}'");
        }

        // php.iniで定義されたアップロード可能な最大ファイルサイズ(upload_max_filesize)を下回る必要がある
        $maxFileSizeBytes = Settings::env('MAX_FILE_SIZE_BYTES');
        if ($_FILES[$REQUEST_PARAM_NAME]['size'] > $maxFileSizeBytes) {
            throw new FileSizeLimitExceededException("File Size Over: file size must be under {$maxFileSizeBytes} bytes.");
        }

        $imageSize = getimagesize($imagePath);
        if ($imageSize === false) {
            throw new InvalidMimeTypeException("Given file is not image.");
        }

        $imageType = $imageSize[2];
        if (
            $imageType !== IMAGETYPE_GIF
            && $imageType !== IMAGETYPE_JPEG
            && $imageType !== IMAGETYPE_PNG
        ) {
            throw new InvalidMimeTypeException('The uploaded image is not in an approved format.');
        }
    }
}
