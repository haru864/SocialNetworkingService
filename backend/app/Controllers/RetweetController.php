<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;
use Http\Request\DeleteRetweetRequest;
use Http\Request\GetRetweetRequest;
use Http\Request\PostRetweetRequest;
use Services\LiveNotificationService;
use Services\RetweetService;

class RetweetController implements ControllerInterface
{
    private RetweetService $retweetService;
    private LiveNotificationService $liveNotificationService;

    public function __construct(RetweetService $retweetService, LiveNotificationService $liveNotificationService)
    {
        $this->retweetService = $retweetService;
        $this->liveNotificationService = $liveNotificationService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $request = new GetRetweetRequest($_GET);
            if (is_null($request->getRetweetId())) {
                return $this->getRetweets($request);
            } else {
                return $this->getRetweetData($request);
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jsonData = file_get_contents('php://input');
            $reqParamMap = json_decode($jsonData, true);
            return $this->postRetweet(new PostRetweetRequest($reqParamMap));
        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            return $this->removeRetweet(new DeleteRetweetRequest());
        }
        throw new InvalidRequestMethodException("Retweet request must be 'GET', 'POST' or 'DELETE'.");
    }

    public function getRetweets(GetRetweetRequest $request): JSONRenderer
    {
        $resp = [];
        $retweets = $this->retweetService->getRetweets($request->getTweetId());
        $isRetweeted = false;
        foreach ($retweets as $retweetArr) {
            if ($retweetArr['userId'] === SessionManager::get('user_id')) {
                $isRetweeted = true;
                break;
            }
        }
        $resp["is_retweeted"] = $isRetweeted;
        $resp["retweets"] = $retweets;
        return new JSONRenderer(200, $resp);
    }

    public function getRetweetData(GetRetweetRequest $request): JSONRenderer
    {
        $resp = [];
        $retweet = $this->retweetService->getRetweet($request->getRetweetId());
        $resp["retweet"] = $retweet->toArray();
        return new JSONRenderer(200, $resp);
    }

    public function postRetweet(PostRetweetRequest $request): JSONRenderer
    {
        $retweet = $this->retweetService->createRetweet(
            SessionManager::get('user_id'),
            $request->getTweetId(),
            $request->getMessage()
        );
        $this->liveNotificationService->publishRetweetNotification($retweet);
        return new JSONRenderer(200, []);
    }

    public function removeRetweet(DeleteRetweetRequest $request): JSONRenderer
    {
        $this->retweetService->removeRetweet(SessionManager::get('user_id'), $request->getTweetId());
        return new JSONRenderer(200, []);
    }
}
