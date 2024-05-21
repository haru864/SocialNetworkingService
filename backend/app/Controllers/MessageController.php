<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;
use Http\Request\DeleteMessagesRequest;
use Http\Request\GetMessagesRequest;
use Http\Request\PostMessageRequest;
use Services\LiveMessageService;
use Services\LiveNotificationService;
use Services\MessageService;
use Services\ProfileService;

class MessageController implements ControllerInterface
{
    private MessageService $messageService;
    private ProfileService $profileService;
    private LiveMessageService $liveMessageService;
    private LiveNotificationService $liveNotificationService;

    public function __construct(
        MessageService $messageService,
        ProfileService $profileService,
        LiveMessageService $liveMessageService,
        LiveNotificationService $liveNotificationService
    ) {
        $this->messageService = $messageService;
        $this->profileService = $profileService;
        $this->liveMessageService = $liveMessageService;
        $this->liveNotificationService = $liveNotificationService;
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
        $userId = SessionManager::get('user_id');
        $recipientUserId = $request->getRecipientUserId();
        $limit = $request->getLimit();
        $offset = ($request->getPage() - 1) * $limit;

        $resp = [];
        if (is_null($request->getRecipientUserId())) {
            $resp = $this->messageService->getChats($userId);
        } else {
            $loginUserProfile = $this->profileService->getUserInfo($userId, true);
            $resp['login_user'] = ['id' => $loginUserProfile['id'], 'name' => $loginUserProfile['name']];
            $chatPartnerProfile = $this->profileService->getUserInfo($recipientUserId, true);
            $resp['chat_partner'] = ['id' => $chatPartnerProfile['id'], 'name' => $chatPartnerProfile['name']];
            $resp['messages'] = $this->messageService->getIndividualChat($userId, $recipientUserId, $limit, $offset);
        }
        return new JSONRenderer(200, $resp);
    }

    private function postMessage(PostMessageRequest $request): JSONRenderer
    {
        $message = $this->messageService->createMessage($request);
        $this->liveMessageService->publishMessage($message);
        $this->liveNotificationService->publishMessageNotification($message);
        return new JSONRenderer(200, []);
    }

    private function deleteMessages(DeleteMessagesRequest $request): JSONRenderer
    {
        $this->messageService->deleteChat(SessionManager::get('user_id'), $request->getRecipientUserId());
        return new JSONRenderer(200, []);
    }
}
