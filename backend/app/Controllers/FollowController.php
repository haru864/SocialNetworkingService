<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;
use Http\Request\GetFollowRequest;
use Http\Request\PostFollowRequest;
use Services\FollowService;
use Services\LiveNotificationService;

class FollowController implements ControllerInterface
{
    private FollowService $followService;
    private LiveNotificationService $liveNotificationService;

    public function __construct(FollowService $followService, LiveNotificationService $liveNotificationService)
    {
        $this->followService = $followService;
        $this->liveNotificationService = $liveNotificationService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $request = new GetFollowRequest($_GET);
            if ($request->getType() === 'follower') {
                return $this->getFollowers($request);
            } else if ($request->getType() === 'followee') {
                return $this->getFollowees($request);
            } else if ($request->getType() === 'follow') {
                return $this->getFollowData($request);
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
                throw new InvalidRequestMethodException("Post-Follow request must be 'application/json'.");
            }
            $jsonData = file_get_contents('php://input');
            $reqParamMap = json_decode($jsonData, true);
            $request = new PostFollowRequest($reqParamMap);
            if ($request->getAction() === 'add') {
                return $this->addFollow($request);
            } else if ($request->getAction() === 'remove') {
                return $this->removeFollow($request);
            }
        } else {
            throw new InvalidRequestMethodException("Follow request must be 'GET' or 'POST'.");
        }
    }

    private function getFollowers(GetFollowRequest $request): JSONRenderer
    {
        $resp = [];
        if (is_null($request->getUserId())) {
            $userId = SessionManager::get('user_id');
        } else {
            $userId = $request->getUserId();
        }
        $resp = $this->followService->getFollowers($userId, $request->getPage(), $request->getLimit());
        return new JSONRenderer(200, $resp);
    }

    private function getFollowees(GetFollowRequest $request): JSONRenderer
    {
        $resp = [];
        if (is_null($request->getUserId())) {
            $userId = SessionManager::get('user_id');
        } else {
            $userId = $request->getUserId();
        }
        $resp = $this->followService->getFollowees($userId, $request->getPage(), $request->getLimit());
        return new JSONRenderer(200, $resp);
    }

    private function getFollowData(GetFollowRequest $request): JSONRenderer
    {
        $followData = $this->followService->getFollowData($request->getFollowId());
        return new JSONRenderer(200, ['follow' => $followData]);
    }

    private function addFollow(PostFollowRequest $request): JSONRenderer
    {
        $resp = [];
        $userId = SessionManager::get('user_id');
        $follow = $this->followService->addFollow($userId, $request->getFolloweeId());
        $this->liveNotificationService->publishFollowNotification($follow);
        return new JSONRenderer(200, $resp);
    }

    private function removeFollow(PostFollowRequest $request): JSONRenderer
    {
        $resp = [];
        $userId = SessionManager::get('user_id');
        $this->followService->removeFollow($userId, $request->getFolloweeId());
        return new JSONRenderer(200, $resp);
    }
}
