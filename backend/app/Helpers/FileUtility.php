<?php

namespace Services;

use Exceptions\InternalServerException;

class FileUtility
{
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

    private function preserveUploadedImageFile(string $newFileBasename): void
    {
        $storagedFilePath = Settings::env('UPLOADED_IMAGE_FILE_LOCATION') . '/' . $newFileBasename;
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            move_uploaded_file($_FILES["image"]["tmp_name"], $storagedFilePath);
        } else {
            throw new InvalidMimeTypeException('Uploaded file was not image-file.');
        }
        $this->createThumbnail($storagedFilePath);
        return;
    }

    private function createThumbnail(string $imageFilePath, int $thumbWidth = 150): void
    {
        $image = new \Imagick($imageFilePath);
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();
        $aspectRatio = $height / $width;
        $thumbHeight = $thumbWidth * $aspectRatio;
        $image->resizeImage($thumbWidth, $thumbHeight, \Imagick::FILTER_LANCZOS, 1);
        $thumbnailFile = Settings::env('THUMBNAIL_FILE_LOCATION') . '/' . basename($imageFilePath);
        $image->writeImage($thumbnailFile);
        $image->clear();
        $image->destroy();
        return;
    }

    private function getFileExtension(): string
    {
        if (isset($_FILES['image']['name'])) {
            $filename = $_FILES['image']['name'];
            $fileInfo = pathinfo($filename);
            $extension = $fileInfo['extension'];
            return $extension;
        }
        throw new InvalidRequestParameterException('No file in request parameter.');
    }
}
