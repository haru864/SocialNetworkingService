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
            return $this->postTweet(new PostTweetRequest($_POST));
        }
        throw new InvalidRequestMethodException("Tweet request must be 'GET' or 'POST'.");
    }

    private function getTweets(GetTweetsRequest $request): JSONRenderer
    {
        return new JSONRenderer(200, []);
    }

    private function postTweet(PostTweetRequest $request): JSONRenderer
    {
        $this->tweetService->createTweet($request);
        return new JSONRenderer(200, []);
    }
}
