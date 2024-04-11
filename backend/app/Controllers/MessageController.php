<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Http\Request\DeleteMessagesRequest;
use Http\Request\GetMessagesRequest;
use Http\Request\PostMessageRequest;
use Services\MessageService;

class MessageController implements ControllerInterface
{
    private MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->getMessages(new GetMessagesRequest($_GET));
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!preg_match('/multipart\/form-data/', $_SERVER['CONTENT_TYPE'])) {
                throw new InvalidRequestMethodException("Request must be 'multipart/form-data'.");
            }
            return $this->postMessage(new PostMessageRequest($_POST, $_FILES));
        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            return $this->deleteMessages(new DeleteMessagesRequest());
        }
        throw new InvalidRequestMethodException("Request must be 'GET' or 'POST'.");
    }

    private function getMessages(GetMessagesRequest $request): JSONRenderer
    {
        $userId = $_SESSION['user_id'];
        $recipientUserId = $request->getRecipientUserId();
        $limit = $request->getLimit();
        $offset = ($request->getPage() - 1) * $limit;

        $resp = [];
        if (is_null($request->getRecipientUserId())) {
            $resp = $this->messageService->getChats($userId);
        } else {
            $resp = $this->messageService->getIndividualChat($userId, $recipientUserId, $limit, $offset);
        }
        return new JSONRenderer(200, $resp);
    }

    private function postMessage(PostMessageRequest $request): JSONRenderer
    {
        $this->messageService->createMessage($request);
        return new JSONRenderer(200, []);
    }

    private function deleteMessages(DeleteMessagesRequest $request): JSONRenderer
    {
        $this->messageService->deleteChat($_SESSION['user_id'], $request->getRecipientUserId());
        return new JSONRenderer(200, []);
    }
}
