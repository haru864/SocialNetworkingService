<?php

namespace Helpers;

use Settings\Settings;
use Exceptions\FileSizeLimitExceededException;
use Exceptions\InvalidMimeTypeException;
use Exceptions\InvalidRequestParameterException;

class ValidationHelper
{
    /**
     * 文字列がNULLでも空文字でもないことをチェックする。
     *
     * @param string $str
     * @return bool NULLまたは空文字ならfalse、それ以外ならtrue
     */
    public static function isNonEmptyString(string $str): bool
    {
        if (is_null($str) || $str === '') {
            return false;
        }
        return true;
    }

    /**
     * multipart/form-dataでアップされたファイルが有効かどうかを検証する。
     * 有効でなければ例外をスローする。
     *
     * @param string $fileKeyName リクエストパラメータにおける画像ファイルのキー名
     * @param array $validMimeTypes 有効なMime-Typeの配列(ex.'image/gif')
     * @return void
     */
    private static function validateUploadedImage(string $fileKeyName, array $validMimeTypes): void
    {
        if ($_FILES[$fileKeyName]['error'] != UPLOAD_ERR_OK) {
            throw new InvalidRequestParameterException("Upload Error: error occured when uploading file.");
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $imagePath = $_FILES[$fileKeyName]['tmp_name'];
        $mimeType = $finfo->file($imagePath);
        if (!in_array($mimeType, $validMimeTypes)) {
            throw new InvalidMimeTypeException("Invalid Mime Type: jpeg, png, gif are allowed. Given MIME-TYPE was '{$mimeType}'");
        }

        // php.iniで定義されたアップロード可能な最大ファイルサイズ(upload_max_filesize)を下回る必要がある
        $maxFileSizeBytes = Settings::env('MAX_FILE_SIZE_BYTES');
        if ($_FILES[$fileKeyName]['size'] > $maxFileSizeBytes) {
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
