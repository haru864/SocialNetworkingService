<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Http\Request\GetRepliesRequest;
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
            return $this->getReplies(new GetRepliesRequest($_GET));
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!preg_match('/multipart\/form-data/', $_SERVER['CONTENT_TYPE'])) {
                throw new InvalidRequestMethodException("Post-Reply request must be 'multipart/form-data'.");
            }
            return $this->postReply(new PostReplyRequest($_POST, $_FILES));
        }
        throw new InvalidRequestMethodException("Reply request must be 'GET' or 'POST'.");
    }

    public function getReplies(GetRepliesRequest $request): JSONRenderer
    {
        $resp = [];
        $replies = $this->replyService->getReplies($request);
        $resp["replies"] = $replies;
        return new JSONRenderer(200, $resp);
    }

    public function postReply(PostReplyRequest $request): JSONRenderer
    {
        $this->replyService->createReply($request);
        return new JSONRenderer(200, []);
    }
}
