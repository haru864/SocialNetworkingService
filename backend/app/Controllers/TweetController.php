<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Exceptions\InvalidRequestParameterException;
use Helpers\SessionManager;
use Http\Request\DeleteTweetRequest;
use Http\Request\GetTweetListRequest;
use Http\Request\GetTweetRequest;
use Http\Request\PostTweetRequest;
use Services\ScheduledTweetService;
use Services\TweetService;

class TweetController implements ControllerInterface
{
    private TweetService $tweetService;
    private ScheduledTweetService $scheduledTweetService;

    public function __construct(
        TweetService $tweetService,
        ScheduledTweetService $scheduledTweetService
    ) {
        $this->tweetService = $tweetService;
        $this->scheduledTweetService = $scheduledTweetService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $getSingleTweetPattern = '/^\/api\/tweets\/[1-9][0-9]*$/';
            if (preg_match($getSingleTweetPattern, $_SERVER['REQUEST_URI'])) {
                return $this->getTweet(new GetTweetRequest());
            } else {
                return $this->getTweets(new GetTweetListRequest($_GET));
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!preg_match('/multipart\/form-data/', $_SERVER['CONTENT_TYPE'])) {
                throw new InvalidRequestMethodException("Post-Tweet request must be 'multipart/form-data'.");
            }
            return $this->postTweet(new PostTweetRequest($_POST, $_FILES));
        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            return $this->deleteTweet(new DeleteTweetRequest());
        }
        throw new InvalidRequestMethodException("Tweet request must be 'GET', 'POST' or 'DELETE'.");
    }

    private function getTweets(GetTweetListRequest $request): JSONRenderer
    {
        $page = $request->getPage();
        $limit = $request->getLimit();

        if ($request->getType() === "trend") {
            $tweets = $this->tweetService->getTweetsByPopular($page, $limit);
        } else if ($request->getType() === "follower") {
            $tweets = $this->tweetService->getTweetsByFollows(SessionManager::get('user_id'), $page, $limit);
        } else if ($request->getType() === "user") {
            if (is_null($request->getUserId())) {
                $userId = SessionManager::get('user_id');
            } else {
                $userId = $request->getUserId();
            }
            $tweets = $this->tweetService->getTweetsByUser($userId, $page, $limit);
        }

        $resp = ["tweets" => $tweets];
        return new JSONRenderer(200, $resp);
    }

    private function getTweet(GetTweetRequest $request): JSONRenderer
    {
        $tweet = $this->tweetService->getTweetById($request->getTweetId());
        $resp = ["tweet" => $tweet];
        return new JSONRenderer(200, $resp);
    }

    private function postTweet(PostTweetRequest $request): JSONRenderer
    {
        if (is_null($request->getScheduledDatetime())) {
            $this->tweetService->createTweet($request);
        } else {
            $this->scheduledTweetService->createScheduledTweet($request);
        }
        return new JSONRenderer(200, []);
    }

    private function deleteTweet(DeleteTweetRequest $request): JSONRenderer
    {
        $loginUserId = SessionManager::get('user_id');
        $tweetId = $request->getTweetId();
        $isTweetFromloginUser = $this->tweetService->isTweetFromSpecifiedUser($loginUserId, $tweetId);
        if (!$isTweetFromloginUser) {
            throw new InvalidRequestParameterException('Only your own tweets can be deleted.');
        }
        $this->tweetService->deleteTweet($tweetId);
        return new JSONRenderer(200, []);
    }
}
