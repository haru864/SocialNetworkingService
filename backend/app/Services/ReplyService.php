<?php

namespace Services;

use Database\DataAccess\Implementations\TweetsDAOImpl;
use Exceptions\InvalidRequestParameterException;
use Helpers\FileUtility;
use Helpers\ValidationHelper;
use Http\Request\GetRepliesRequest;
use Http\Request\PostReplyRequest;
use Models\Tweet;
use Settings\Settings;

class ReplyService
{
    private TweetsDAOImpl $tweetsDAOImpl;

    public function __construct(TweetsDAOImpl $tweetsDAOImpl)
    {
        $this->tweetsDAOImpl = $tweetsDAOImpl;
    }

    public function createReply(PostReplyRequest $request): void
    {
        $currentDatetime = date('Y-m-d H:i:s');
        $tweetMessage = $request->getMessage();
        $maxTweetMsgChars = 200;
        if (mb_strlen($tweetMessage) < 1 || mb_strlen($tweetMessage) > $maxTweetMsgChars) {
            throw new InvalidRequestParameterException("Reply must be at least 1 character and no more than {$maxTweetMsgChars} characters");
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
            replyToId: $request->getTweetId(),
            userId: $_SESSION['user_id'],
            message: $tweetMessage,
            mediaFileName: $mediaFileName,
            mediaType: $mimeType,
            postingDatetime: $currentDatetime
        );
        $this->tweetsDAOImpl->create($tweet);
        return;
    }

    public function getRepliedTweet(GetRepliesRequest $request): ?array
    {
        $tweetId = $request->getTweetId();
        $tweet  = $this->tweetsDAOImpl->getByTweetId($tweetId);
        return is_null($tweet) ? null : $tweet->toArray();
    }

    public function getReplies(GetRepliesRequest $request): array
    {
        $page = $request->getPage();
        $limit = $request->getLimit();
        $offset = ($page - 1) * $limit;
        $replies = $this->tweetsDAOImpl->getByReplyToId($request->getTweetId(), $limit, $offset);
        $replyArr = ["replies" => []];
        foreach ($replies as $reply) {
            array_push($replyArr["replies"], $reply->toArray());
        }
        return $replyArr;
    }
}
