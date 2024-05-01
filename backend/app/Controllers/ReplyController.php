<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Http\Request\GetRepliesAllRequest;
use Http\Request\GetRepliesPartRequest;
use Http\Request\PostReplyRequest;
use Services\ReplyService;

class ReplyController implements ControllerInterface
{
    private ReplyService $replyService;

    public function __construct(ReplyService $replyService)
    {
        $this->replyService = $replyService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (empty($_SERVER['QUERY_STRING'])) {
                return $this->getAllReplies(new GetRepliesAllRequest());
            } else {
                return $this->getPartialReplies(new GetRepliesPartRequest($_GET));
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!preg_match('/multipart\/form-data/', $_SERVER['CONTENT_TYPE'])) {
                throw new InvalidRequestMethodException("Post-Reply request must be 'multipart/form-data'.");
            }
            return $this->postReply(new PostReplyRequest($_POST, $_FILES));
        }
        throw new InvalidRequestMethodException("Reply request must be 'GET' or 'POST'.");
    }

    public function getAllReplies(GetRepliesAllRequest $request): JSONRenderer
    {
        $resp = [];
        $replies = $this->replyService->getAllReplies($request->getTweetId());
        $resp["replies"] = $replies;
        return new JSONRenderer(200, $resp);
    }

    public function getPartialReplies(GetRepliesPartRequest $request): JSONRenderer
    {
        $resp = [];
        $replies = $this->replyService->getPartialReplies($request->getTweetId(), $request->getPage(), $request->getLimit());
        $resp["replies"] = $replies;
        return new JSONRenderer(200, $resp);
    }

    public function postReply(PostReplyRequest $request): JSONRenderer
    {
        $this->replyService->createReply($request);
        return new JSONRenderer(200, []);
    }
}
