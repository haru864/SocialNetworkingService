<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;
use Http\Request\LikeRequest;
use Services\LikeService;

class LikeController implements ControllerInterface
{
    private LikeService $likeService;

    public function __construct(LikeService $likeService)
    {
        $this->likeService = $likeService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new InvalidRequestMethodException("Like request must be 'POST'.");
        }
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            throw new InvalidRequestMethodException("Like request must be 'application/json'.");
        }
        $jsonData = file_get_contents('php://input');
        $reqParamMap = json_decode($jsonData, true);
        $request = new LikeRequest($reqParamMap);
        if ($request->getAction() === 'add_like') {
            return $this->addLike($request);
        } else if ($request->getAction() === 'remove_like') {
            return $this->removeLike($request);
        } else if ($request->getAction() === 'get_users') {
            return $this->getUsers($request);
        }
    }

    private function addLike(LikeRequest $request): JSONRenderer
    {
        $this->likeService->addLike(SessionManager::get('user_id'), $request->getTweetId());
        return new JSONRenderer(200, []);
    }

    private function removeLike(LikeRequest $request): JSONRenderer
    {
        $this->likeService->removeLike(SessionManager::get('user_id'), $request->getTweetId());
        return new JSONRenderer(200, []);
    }

    private function getUsers(LikeRequest $request): JSONRenderer
    {
        $userIds = $this->likeService->getLikeUsers($request->getTweetId());
        return new JSONRenderer(200, ["user_id" => $userIds]);
    }
}
