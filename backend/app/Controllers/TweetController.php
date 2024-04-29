<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Http\Request\GetTweetsRequest;
use Http\Request\PostTweetRequest;
use Services\TweetService;

class TweetController implements ControllerInterface
{
    private TweetService $tweetService;

    public function __construct(TweetService $tweetService)
    {
        $this->tweetService = $tweetService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->getTweets(new GetTweetsRequest($_GET));
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!preg_match('/multipart\/form-data/', $_SERVER['CONTENT_TYPE'])) {
                throw new InvalidRequestMethodException("Post-Tweet request must be 'multipart/form-data'.");
            }
            return $this->postTweet(new PostTweetRequest($_POST, $_FILES));
        }
        throw new InvalidRequestMethodException("Tweet request must be 'GET' or 'POST'.");
    }

    // TODO リツイートも返したい
    private function getTweets(GetTweetsRequest $request): JSONRenderer
    {
        if ($request->getType() === "trend") {
            $tweets = $this->tweetService->getTweetsByPopular($request);
        } else if ($request->getType() === "follower") {
            $tweets = $this->tweetService->getTweetsByFollows($request);
        } else if ($request->getType() === "user") {
            $tweets = $this->tweetService->getTweetsByUser($request);
        }
        $resp = ["tweets" => $tweets];
        return new JSONRenderer(200, $resp);
    }

    private function postTweet(PostTweetRequest $request): JSONRenderer
    {
        $this->tweetService->createTweet($request);
        return new JSONRenderer(200, []);
    }
}
