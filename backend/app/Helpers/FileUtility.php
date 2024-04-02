<?php

namespace Helpers;

use Exceptions\InternalServerException;
use Exceptions\InvalidMimeTypeException;

class FileUtility
{
    /**
     * リクエストパラメータのファイルとそのサムネイルを指定のディレクトリに保存する。
     *
     * @param string $storeDirPath アップロードされた画像ファイルを保存するディレクトリ
     * @param string $thumbDirPath サムネイルを保存するディレクトリ
     * @param string $uploadedTmpFilePath アップロードされたファイルがサーバー上で保存されているテンポラリファイルの名前($_FILES['userfile']['tmp_name'])
     * @param string $uploadedFileName クライアントマシンの元のファイル名($_FILES['userfile']['name'])
     * @param int $thumbWidth サムネイルの幅
     * @return string 保存されたファイルの名前(拡張子付き)
     */
    public static function storeImageWithThumbnail(
        string $storeDirPath,
        string $thumbDirPath,
        string $uploadedTmpFilePath,
        string $uploadedFileName,
        int $thumbWidth
    ): string {
        $hash = FileUtility::generateUniqueHashWithLimit($storeDirPath, $uploadedFileName);
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($uploadedTmpFilePath);
        $validImageMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif'
        ];
        if (!array_key_exists($mimeType, $validImageMimeTypes)) {
            throw new InvalidMimeTypeException("Invalid Mime-Type: '{$mimeType}'");
        }
        $imageFileExtension = $validImageMimeTypes[$mimeType];
        $storedImageFileName = $hash . '.' . $imageFileExtension;
        $storedImageFilePath = $storeDirPath . DIRECTORY_SEPARATOR . $storedImageFileName;
        if (!move_uploaded_file($uploadedTmpFilePath, $storedImageFilePath)) {
            throw new InternalServerException("Failed to move uploaded file.");
        }
        FileUtility::createThumbnail($storedImageFilePath, $thumbDirPath, $thumbWidth);
        return $storedImageFileName;
    }

    // TODO 圧縮処理を追加する
    /**
     * リクエストパラメータの動画ファイルを圧縮して指定のディレクトリに保存する。
     *
     * @param string $storeDirPath アップロードされた動画ファイルを保存するディレクトリ
     * @param string $uploadedTmpFilePath アップロードされたファイルがサーバー上で保存されているテンポラリファイルの名前($_FILES['userfile']['tmp_name'])
     * @param string $uploadedFileName クライアントマシンの元のファイル名($_FILES['userfile']['name'])
     * @return string 保存されたファイルの名前(拡張子付き)
     */
    public static function storeVideo(
        string $storeDirPath,
        string $uploadedTmpFilePath,
        string $uploadedFileName
    ): string {
        $hash = FileUtility::generateUniqueHashWithLimit($storeDirPath, $uploadedFileName);
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($uploadedTmpFilePath);
        $validVideoMimeTypes = [
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            'video/ogg' => 'ogg'
        ];
        if (!array_key_exists($mimeType, $validVideoMimeTypes)) {
            throw new InvalidMimeTypeException("Invalid Mime-Type: '{$mimeType}'");
        }
        $videoFileExtension = $validVideoMimeTypes[$mimeType];
        $storedVideoFileName = $hash . '.' . $videoFileExtension;
        $storedVideoFilePath = $storeDirPath . DIRECTORY_SEPARATOR . $storedVideoFileName;
        if (!move_uploaded_file($uploadedTmpFilePath, $storedVideoFilePath)) {
            throw new InternalServerException("Failed to move uploaded file.");
        }
        return $storedVideoFileName;
    }

    private static function generateUniqueHashWithLimit(string $dirPath, string $data, $limit = 100): string
    {
        $iterator = new \DirectoryIterator($dirPath);
        $hash = hash('sha256', $data);
        $counter = 0;
        while ($counter < $limit) {
            foreach ($iterator as $fileinfo) {
                if (!$fileinfo->isDot()) {
                    $filenameWithoutExtension = $fileinfo->getBasename('.' . $fileinfo->getExtension());
                }
                if ($hash === $filenameWithoutExtension) {
                    break;
                }
                return $hash;
            }
            $counter++;
            $hash = hash('sha256', $data . $counter);
        }
        throw new InternalServerException('Failed to generate unique hash value.');
    }

    private static function createThumbnail(string $imageFilePath, string $thumbDirPath, int $thumbWidth = 100): void
    {
        $image = new \Imagick($imageFilePath);
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();
        $aspectRatio = $height / $width;
        $thumbHeight = $thumbWidth * $aspectRatio;
        $image->resizeImage($thumbWidth, $thumbHeight, \Imagick::FILTER_LANCZOS, 1);
        $thumbnailFile = $thumbDirPath . DIRECTORY_SEPARATOR . basename($imageFilePath);
        $image->writeImage($thumbnailFile);
        $image->clear();
        $image->destroy();
        return;
    }
}
