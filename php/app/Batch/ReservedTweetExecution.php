<?php

spl_autoload_extensions(".php");
spl_autoload_register(function ($class) {
    $class = str_replace("\\", "/", $class);
    $file = __DIR__ . "/../" . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Database\DataAccess\Implementations\ScheduledTweetsDAOImpl;
use Database\DataAccess\Implementations\TweetsDAOImpl;
use Helpers\FileUtility;
use Logging\Logger;
use Services\ScheduledTweetService;
use Settings\Settings;

date_default_timezone_set('Asia/Tokyo');
$logger = Logger::getInstance();

try {
    $logger->logInfo('[Reservation Submission] Start Execution.');
    $scheduledTweetServide = new ScheduledTweetService(
        new ScheduledTweetsDAOImpl,
        new TweetsDAOImpl
    );
    $result = null;
    $result = $scheduledTweetServide->createTweetByScheduled();
    $logger->logInfo('[Reservation Submission] Finish successfully.');
} catch (Throwable $t) {
    $logger->logInfo('[Reservation Submission] Terminate with error.');
    $logger->logError($t);
} finally {
    $logger->logInfo('[Reservation Submission] Display result.' . PHP_EOL . json_encode($result->toArray()));
}

function deleteScheduledTweetMediaFile(string $mediaFileName, string $mediaType): void
{
    $imagePattern = '/^image\/(jpeg|gif|png)$/';
    $videoPattern = '/^video\/(mp4|webm|ogg)$/';
    if (preg_match($imagePattern, $mediaType)) {
        FileUtility::deleteImageWithThumbnail(
            storeDirPath: Settings::env('IMAGE_FILE_LOCATION_TWEET_UPLOAD'),
            thumbDirPath: Settings::env('IMAGE_FILE_LOCATION_TWEET_THUMBNAIL'),
            imageFileName: $mediaFileName
        );
    } elseif (preg_match($videoPattern, $mediaType)) {
        FileUtility::deleteVideo(
            storeDirPath: Settings::env('VIDEO_FILE_LOCATION_TWEET'),
            videoFileName: $mediaFileName
        );
    }
}
