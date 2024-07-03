<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Helpers\SessionManager;
use Http\Request\NotificationRequest;
use Render\Interface\HTTPRenderer;
use Render\JSONRenderer;
use Services\NotificationService;

class NotificationController implements ControllerInterface
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handleRequest(): HTTPRenderer
    {
        $request = new NotificationRequest($_GET);
        $limit = $request->getLimit();
        $page = $request->getPage();
        $offset = ($page - 1) * $limit;
        $userId = SessionManager::get('user_id');
        $notifications = $this->notificationService->getAllNotificationsSorted($userId, $limit, $offset);
        return new JSONRenderer(200, ['notifications' => $notifications]);
    }
}
