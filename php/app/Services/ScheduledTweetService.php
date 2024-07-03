<?php

namespace Services;

use Database\DataAccess\Implementations\ScheduledTweetsDAOImpl;
use Database\DataAccess\Implementations\TweetsDAOImpl;
use Exceptions\InvalidRequestParameterException;
use Helpers\FileUtility;
use Helpers\SessionManager;
use Helpers\ValidationHelper;
use Http\Request\PostTweetRequest;
use Models\error\ScheduledPostError;
use Models\ScheduledPostResult;
use Models\ScheduledTweet;
use Models\Tweet;
use Settings\Settings;
use Throwable;

class ScheduledTweetService
{
    private ScheduledTweetsDAOImpl $scheduledTweetsDAOImpl;
    private TweetsDAOImpl $tweetsDAOImpl;

    public function __construct(ScheduledTweetsDAOImpl $scheduledTweetsDAOImpl, TweetsDAOImpl $tweetsDAOImpl)
    {
        $this->scheduledTweetsDAOImpl = $scheduledTweetsDAOImpl;
        $this->tweetsDAOImpl = $tweetsDAOImpl;
    }

    public function createScheduledTweet(PostTweetRequest $request): void
    {
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
        $scheduledTweet = new ScheduledTweet(
            id: null,
            replyToId: null,
            userId: SessionManager::get('user_id'),
            message: $tweetMessage,
            mediaFileName: $mediaFileName,
            mediaType: $mimeType,
            scheduledDatetime: $request->getScheduledDatetime()
        );
        $this->scheduledTweetsDAOImpl->create($scheduledTweet);
        return;
    }

    public function createTweetByScheduled(): ScheduledPostResult
    {
        $currentDatetime = date('Y-m-d H:i:s');
        $scheduledTweets = $this->scheduledTweetsDAOImpl->getByScheduled($currentDatetime);
        $scheduledTweetCount = count($scheduledTweets);
        $postedTweetCount = 0;
        $errors = [];
        foreach ($scheduledTweets as $scheduledTweet) {
            try {
                $tweet = new Tweet(
                    id: null,
                    replyToId: $scheduledTweet->getReplyToId(),
                    retweetToId: null,
                    userId: $scheduledTweet->getUserId(),
                    message: $scheduledTweet->getMessage(),
                    mediaFileName: $scheduledTweet->getMediaFileName(),
                    mediaType: $scheduledTweet->getMediaType(),
                    postingDatetime: $scheduledTweet->getScheduledDatetime()
                );
                $this->tweetsDAOImpl->create($tweet);
                $postedTweetCount++;
                $this->scheduledTweetsDAOImpl->deleteById($scheduledTweet->getId());
            } catch (Throwable $t) {
                $scheduledPostError = new ScheduledPostError(
                    postInfo: json_encode($tweet),
                    errorMessage: $t->getMessage()
                );
                array_push($errors, $scheduledPostError);
            }
        }
        return new ScheduledPostResult(
            scheduledTweetCount: $scheduledTweetCount,
            postedTweetCount: $postedTweetCount,
            scheduledPostError: $errors
        );
    }
}
