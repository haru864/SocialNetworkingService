<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;
use Http\Request\GetLikeRequest;
use Http\Request\UpdateLikeRequest;
use Services\LikeService;
use Services\LiveNotificationService;

class LikeController implements ControllerInterface
{
    private LikeService $likeService;
    private LiveNotificationService $liveNotificationService;

    public function __construct(LikeService $likeService, LiveNotificationService $liveNotificationService)
    {
        $this->likeService = $likeService;
        $this->liveNotificationService = $liveNotificationService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $request = new GetLikeRequest($_GET);
            if (!is_null($request->getTweetId())) {
                return $this->getLikeUsers($request);
            } else if (!is_null($request->getLikeId())) {
                return $this->getLikeData($request);
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
                throw new InvalidRequestMethodException("Like request must be 'application/json'.");
            }
            $jsonData = file_get_contents('php://input');
            $reqParamMap = json_decode($jsonData, true);
            $request = new UpdateLikeRequest($reqParamMap);
            if ($request->getAction() === 'add') {
                return $this->addLike($request);
            } else if ($request->getAction() === 'remove') {
                return $this->removeLike($request);
            }
        } else {
            throw new InvalidRequestMethodException("Like request must be 'GET' or 'POST'.");
        }
    }

    private function getLikeUsers(GetLikeRequest $request): JSONRenderer
    {
        $isLiked = $this->likeService->checkLiked(SessionManager::get('user_id'), $request->getTweetId());
        $userIds = $this->likeService->getLikeUsers($request->getTweetId());
        return new JSONRenderer(200, ["is_liked" => $isLiked, "user_id" => $userIds]);
    }

    private function getLikeData(GetLikeRequest $request): JSONRenderer
    {
        $like = $this->likeService->getLikeData($request->getLikeId());
        return new JSONRenderer(200, ['like_data' => $like->toArray()]);
    }

    private function addLike(UpdateLikeRequest $request): JSONRenderer
    {
        $like = $this->likeService->addLike(SessionManager::get('user_id'), $request->getTweetId());
        $this->liveNotificationService->publishLikeNotification($like);
        return new JSONRenderer(200, []);
    }

    private function removeLike(UpdateLikeRequest $request): JSONRenderer
    {
        $this->likeService->removeLike(SessionManager::get('user_id'), $request->getTweetId());
        return new JSONRenderer(200, []);
    }
}
