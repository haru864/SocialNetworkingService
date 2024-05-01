<?php

namespace Services;

use Database\DataAccess\Implementations\TweetsDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;
use Exceptions\InvalidRequestParameterException;
use Helpers\FileUtility;
use Helpers\SessionManager;
use Helpers\ValidationHelper;
use Http\Request\GetTweetsRequest;
use Http\Request\LoginRequest;
use Http\Request\PostTweetRequest;
use Models\Tweet;
use Models\User;
use Settings\Settings;

class TweetService
{
    private TweetsDAOImpl $tweetsDAOImpl;

    public function __construct(TweetsDAOImpl $tweetsDAOImpl)
    {
        $this->tweetsDAOImpl = $tweetsDAOImpl;
    }

    public function createTweet(PostTweetRequest $request): void
    {
        $currentDatetime = date('Y-m-d H:i:s');
        $tweetMessage = $request->getMessage();
        $maxTweetMsgChars = 200;
        if (mb_strlen($tweetMessage) < 1 || mb_strlen($tweetMessage) > $maxTweetMsgChars) {
            throw new InvalidRequestParameterException("Tweets must be at least 1 character and no more than {$maxTweetMsgChars} characters");
        }

        if (is_null($request->getMedia())) {
            $mediaFileName = null;
            $mimeType = null;
        } else {
            $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $fileInfo->file($request->getMedia()['tmp_name']);
            if (strpos($mimeType, 'image/') === 0) {
                ValidationHelper::validateUploadedImage('media');
                $mediaFileName = FileUtility::storeImageWithThumbnail(
                    storeDirPath: Settings::env('IMAGE_FILE_LOCATION_TWEET_UPLOAD'),
                    thumbDirPath: Settings::env('IMAGE_FILE_LOCATION_TWEET_THUMBNAIL'),
                    uploadedTmpFilePath: $request->getMedia()['tmp_name'],
                    uploadedFileName: $request->getMedia()['name'],
                    thumbWidth: 200
                );
            } else if (strpos($mimeType, 'video/') === 0) {
                ValidationHelper::validateUploadedVideo('media');
                $mediaFileName = FileUtility::storeVideo(
                    storeDirPath: Settings::env('VIDEO_FILE_LOCATION_TWEET'),
                    uploadedTmpFilePath: $request->getMedia()['tmp_name'],
                    uploadedFileName: $request->getMedia()['name']
                );
            } else {
                throw new InvalidRequestParameterException("Attachments to tweets must be images or videos.");
            }
        }

        $tweet = new Tweet(
            id: null,
            replyToId: null,
            userId: SessionManager::get('user_id'),
            message: $tweetMessage,
            mediaFileName: $mediaFileName,
            mediaType: $mimeType,
            postingDatetime: $currentDatetime
        );
        $this->tweetsDAOImpl->create($tweet);
        return;
    }

    public function getTweetsByUser(int $userId, int $page, int $limit): ?array
    {
        $offset = ($page - 1) * $limit;
        $tweets = $this->tweetsDAOImpl->getByUserId($userId, $limit, $offset);
        $tweetArr = [];
        foreach ($tweets as $tweet) {
            array_push($tweetArr, $tweet->toArray());
        }
        return $tweetArr;
    }

    public function getTweetsByPopular(int $page, int $limit): ?array
    {
        $offset = ($page - 1) * $limit;
        $tweets = $this->tweetsDAOImpl->getByPopular($limit, $offset);
        $tweetArr = [];
        foreach ($tweets as $tweet) {
            array_push($tweetArr, $tweet->toArray());
        }
        return $tweetArr;
    }

    public function getTweetsByFollows(int $userId, int $page, int $limit): ?array
    {
        $offset = ($page - 1) * $limit;
        $tweets = $this->tweetsDAOImpl->getByFollower($userId, $limit, $offset);
        $tweetArr = [];
        foreach ($tweets as $tweet) {
            array_push($tweetArr, $tweet->toArray());
        }
        return $tweetArr;
    }

    public function getTweetById(int $tweetId): array
    {
        $tweet = $this->tweetsDAOImpl->getByTweetId($tweetId);
        return $tweet->toArray();
    }

    public function deleteTweet(int $tweetId): void
    {
        $tweet = $this->tweetsDAOImpl->getByTweetId($tweetId);
        if (is_null($tweet)) {
            throw new InvalidRequestParameterException('Tweet does not exist.');
        }
        $this->tweetsDAOImpl->deleteById($tweetId);
        return;
    }
}
