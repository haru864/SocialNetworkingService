<?php

use Database\DataAccess\Implementations\TweetsDAOImpl;
use Models\Tweet;
use Settings\Settings;

require_once __DIR__ . '/../../vendor/autoload.php';

spl_autoload_extensions(".php");
spl_autoload_register(function ($class) {
    $class = str_replace("\\", "/", $class);
    $file = __DIR__ . "/../../app/" . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

date_default_timezone_set('Asia/Tokyo');
$faker = Faker\Factory::create();
$tweetsDAOImpl = new TweetsDAOImpl();
$tweetsCount = 50;

// $options = getopt("", ['userId:']);
// $tweetUserId = (int)$options['userId'];
$tweetUserId = 1;

for ($i = 0; $i < $tweetsCount; $i++) {
    $nowDateTime = new DateTime();
    $nowDateTimeStr = $nowDateTime->format('Y-m-d H:i:s');
    $imagePath = __DIR__ . '/images/random_image.png';
    generateImage($imagePath);
    $tweetImage = storeImageWithThumbnail(
        storeDirPath: Settings::env('IMAGE_FILE_LOCATION_TWEET_UPLOAD'),
        thumbDirPath: Settings::env('IMAGE_FILE_LOCATION_TWEET_THUMBNAIL'),
        uploadedTmpFilePath: $imagePath,
        uploadedFileName: 'random_image.png',
        thumbWidth: 200
    );
    $tweet = new Tweet(
        id: null,
        replyToId: null,
        retweetToId: null,
        userId: $tweetUserId,
        message: $faker->text(200),
        mediaFileName: $tweetImage,
        mediaType: "image/png",
        postingDatetime: $nowDateTimeStr
    );
    $tweetsDAOImpl->create($tweet);
}

function storeImageWithThumbnail(
    string $storeDirPath,
    string $thumbDirPath,
    string $uploadedTmpFilePath,
    string $uploadedFileName,
    int $thumbWidth
): string {
    $hash = generateUniqueHashWithLimit($storeDirPath, $uploadedFileName);
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($uploadedTmpFilePath);
    $validImageMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif'
    ];
    if (!array_key_exists($mimeType, $validImageMimeTypes)) {
        throw new Exception("Invalid Mime-Type: '{$mimeType}'");
    }
    $imageFileExtension = $validImageMimeTypes[$mimeType];
    $storedImageFileName = $hash . '.' . $imageFileExtension;
    $storedImageFilePath = $storeDirPath . DIRECTORY_SEPARATOR . $storedImageFileName;
    if (!rename($uploadedTmpFilePath, $storedImageFilePath)) {
        throw new Exception("Failed to move uploaded file. ({$uploadedTmpFilePath} => {$storedImageFilePath})");
    }
    createThumbnail($storedImageFilePath, $thumbDirPath, $thumbWidth);
    return $storedImageFileName;
}

function generateImage(string $filePath): void
{
    $width = rand(100, 1000);
    $height = rand(100, 1000);
    $image = imagecreatetruecolor($width, $height);
    $backgroundColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
    imagefill($image, 0, 0, $backgroundColor);
    $lineColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
    imagerectangle($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
    imagepng($image, $filePath);
    imagedestroy($image);
}

function generateUniqueHashWithLimit(string $dirPath, string $data, $limit = 1000): string
{
    $iterator = new \DirectoryIterator($dirPath);
    $hash = hash('sha256', $data);
    $counter = 0;
    while ($counter < $limit) {
        $unique = true;
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDot()) {
                continue;
            }
            $filenameWithoutExtension = $fileinfo->getBasename('.' . $fileinfo->getExtension());
            if ($hash === $filenameWithoutExtension) {
                $unique = false;
            }
        }
        if ($unique) {
            return $hash;
        }
        $counter++;
        $hash = hash('sha256', $data . $counter);
    }
    throw new Exception('Failed to generate unique hash value.');
}

function createThumbnail(string $imageFilePath, string $thumbDirPath, int $thumbWidth = 100): void
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
