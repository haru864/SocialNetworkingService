<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;
use Http\Request\RetweetsRequest;
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
            return $this->getRetweets(new RetweetsRequest());
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->postRetweet(new RetweetsRequest());
        }
        throw new InvalidRequestMethodException("Retweet request must be 'GET' or 'POST'.");
    }

    public function getRetweets(RetweetsRequest $request): JSONRenderer
    {
        $resp["retweets"] = $this->retweetService->getRetweets($request);
        return new JSONRenderer(200, $resp);
    }

    public function postRetweet(RetweetsRequest $request): JSONRenderer
    {
        $this->retweetService->createRetweet($request, SessionManager::get('user_id'));
        return new JSONRenderer(200, []);
    }
}
