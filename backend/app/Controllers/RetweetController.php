<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Http\Request\GetTweetsRequest;
use Http\Request\PostTweetRequest;
use Services\TweetService;

class RetweetController implements ControllerInterface
{
    private TweetService $tweetService;

    public function __construct(TweetService $tweetService)
    {
        $this->tweetService = $tweetService;
    }
}
