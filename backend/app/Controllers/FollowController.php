<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Http\Request\FollowRequest;
use Services\FollowService;

class FollowController implements ControllerInterface
{
    private FollowService $followService;

    public function __construct(FollowService $followService)
    {
        $this->followService = $followService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new InvalidRequestMethodException("Follow request must be 'POST'.");
        }
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            throw new InvalidRequestMethodException("Follow request must be 'application/json'.");
        }
        $jsonData = file_get_contents('php://input');
        $reqParamMap = json_decode($jsonData, true);
        return $this->follow(new FollowRequest($reqParamMap));
    }

    private function follow(FollowRequest $request): JSONRenderer
    {
        $resp = [];
        $userId = $_SESSION['user_id'];
        if ($request->getAction() === 'add_follow') {
            $this->followService->addFollow($userId, $request->getFolloweeId());
        } else if ($request->getAction() === 'remove_follow') {
            $this->followService->removeFollow($userId, $request->getFolloweeId());
        } else if ($request->getAction() === 'get_followers') {
            $resp = $this->followService->getFollowers($userId);
        } else if ($request->getAction() === 'get_followees') {
            $resp = $this->followService->getFollowees($userId);
        }
        return new JSONRenderer(200, $resp);
    }
}
