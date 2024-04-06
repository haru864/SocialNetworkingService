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
        $request = new FollowRequest($reqParamMap);
        if ($request->getAction() === 'add_follow') {
            return $this->addFollow($request);
        } else if ($request->getAction() === 'remove_follow') {
            return $this->removeFollow($request);
        } else if ($request->getAction() === 'get_followers') {
            return $this->getFollowers($request);
        } else if ($request->getAction() === 'get_followees') {
            return $this->getFollowees($request);
        }
    }

    private function addFollow(FollowRequest $request): JSONRenderer
    {
        $resp = [];
        $userId = $_SESSION['user_id'];
        $this->followService->addFollow($userId, $request->getFolloweeId());
        return new JSONRenderer(200, $resp);
    }

    private function removeFollow(FollowRequest $request): JSONRenderer
    {
        $resp = [];
        $userId = $_SESSION['user_id'];
        $this->followService->removeFollow($userId, $request->getFolloweeId());
        return new JSONRenderer(200, $resp);
    }

    private function getFollowers(FollowRequest $request): JSONRenderer
    {
        $resp = [];
        $userId = $_SESSION['user_id'];
        $resp = $this->followService->getFollowers($userId);
        return new JSONRenderer(200, $resp);
    }

    private function getFollowees(FollowRequest $request): JSONRenderer
    {
        $resp = [];
        $userId = $_SESSION['user_id'];
        $resp = $this->followService->getFollowees($userId);
        return new JSONRenderer(200, $resp);
    }
}
