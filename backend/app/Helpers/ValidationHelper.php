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

    public static function validateUsername(string $username): void
    {
        $pattern = '/^[a-zA-Z0-9]{1,15}$/';
        if (!preg_match($pattern, $username)) {
            throw new InvalidRequestParameterException('Username must be alphanumeric characters only and limited to 15 characters.');
        }
        return;
    }

    public static function validatePassword(string $password): void
    {
        $invalidCharsPattern = '/[^a-zA-Z0-9!-\/:-@\[-`\{-~]/';
        if (preg_match($invalidCharsPattern, $password)) {
            throw new InvalidRequestParameterException('Only single-byte alphanumeric characters and symbols can be used.');
        }

        $typesIncluded = 0;
        if (preg_match('/[a-z]/', $password)) {
            $typesIncluded++;
        }
        if (preg_match('/[A-Z]/', $password)) {
            $typesIncluded++;
        }
        if (preg_match('/[0-9]/', $password)) {
            $typesIncluded++;
        }
        if (preg_match('/[!-\/:-@\[-`\{-~]/', $password)) {
            $typesIncluded++;
        }
        $minTypesCount = 4;
        if ($typesIncluded < $minTypesCount) {
            throw new InvalidRequestParameterException('Include all four types of uppercase and lowercase letters, numbers and symbols.');
        }

        $minPasswordChars = 8;
        if (strlen($password) < $minPasswordChars) {
            throw new InvalidRequestParameterException('Password must be at least 8 characters.');
        }
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
