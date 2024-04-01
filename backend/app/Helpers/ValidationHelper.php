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
     * 文字列が正の整数として解釈可能かどうかをチェックする。
     *
     * @param string $str
     * @return bool 正の整数として解釈可能ならtrue、それ以外ならfalse
     */
    public static function isPositiveIntegerString(string $str): bool
    {
        return preg_match('/^[1-9][0-9]*$/', $str) === 1;
    }

    /**
     * multipart/form-dataでアップされた画像ファイルが有効かどうかを検証する。
     * 有効でなければ例外をスローする。
     *
     * @param string $fileKeyName リクエストパラメータにおける画像ファイルのキー名
     * @return void
     */
    public static function validateUploadedImage(string $fileKeyName): void
    {
        if ($_FILES[$fileKeyName]['error'] != UPLOAD_ERR_OK) {
            throw new InvalidRequestParameterException("Upload Error: error occured when uploading file.");
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $imagePath = $_FILES[$fileKeyName]['tmp_name'];
        $mimeType = $finfo->file($imagePath);
        $validMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mimeType, $validMimeTypes)) {
            $validMimeTypeStr = implode(",", $validMimeTypes);
            throw new InvalidMimeTypeException("{$validMimeTypeStr} are allowed. ('{$mimeType}' given)");
        }

        $maxFileSizeBytes = Settings::env('MAX_IMAGE_SIZE_BYTES');
        if ($_FILES[$fileKeyName]['size'] > $maxFileSizeBytes) {
            throw new FileSizeLimitExceededException("File Size Over: file size must be under {$maxFileSizeBytes} bytes.");
        }
    }

    /**
     * multipart/form-dataでアップされた動画ファイルが有効かどうかを検証する。
     * 有効でなければ例外をスローする。
     *
     * @param string $fileKeyName リクエストパラメータにおける動画ファイルのキー名
     * @return void
     */
    public static function validateUploadedVideo(string $fileKeyName): void
    {
        if ($_FILES[$fileKeyName]['error'] != UPLOAD_ERR_OK) {
            throw new InvalidRequestParameterException("Upload Error: error occured when uploading file.");
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $videoFilePath = $_FILES[$fileKeyName]['tmp_name'];
        $mimeType = $finfo->file($videoFilePath);
        $validMimeTypes = ['video/mp4', 'video/webm', 'video/ogg'];
        if (!in_array($mimeType, $validMimeTypes)) {
            $validMimeTypeStr = implode(",", $validMimeTypes);
            throw new InvalidMimeTypeException("{$validMimeTypeStr} are allowed. ('{$mimeType}' given)");
        }

        $maxFileSizeBytes = Settings::env('MAX_VIDEO_SIZE_BYTES');
        if ($_FILES[$fileKeyName]['size'] > $maxFileSizeBytes) {
            throw new FileSizeLimitExceededException("File Size Over: file size must be under {$maxFileSizeBytes} bytes.");
        }
    }
}
