<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;
use Http\Request\DeleteRetweetRequest;
use Http\Request\GetRetweetRequest;
use Http\Request\PostRetweetRequest;
use Services\RetweetService;

class RetweetController implements ControllerInterface
{
    private RetweetService $retweetService;

    public function __construct(RetweetService $retweetService)
    {
        $this->retweetService = $retweetService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->getRetweets(new GetRetweetRequest());
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
        $resp["retweets"] = $this->retweetService->getRetweets($request->getTweetId());
        return new JSONRenderer(200, $resp);
    }

    public function postRetweet(PostRetweetRequest $request): JSONRenderer
    {
        $this->retweetService->createRetweet(
            SessionManager::get('user_id'),
            $request->getTweetId(),
            $request->getMessage()
        );
        return new JSONRenderer(200, []);
    }

    public function removeRetweet(DeleteRetweetRequest $request): JSONRenderer
    {
        $this->retweetService->removeRetweet(SessionManager::get('user_id'), $request->getTweetId());
        return new JSONRenderer(200, []);
    }
}
