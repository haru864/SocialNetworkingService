<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Exceptions\InvalidRequestMethodException;
use Helpers\SessionManager;
use Render\Interface\HTTPRenderer;
use Render\JSONRenderer;
use Services\NotificationService;

class NotificationConfirmController implements ControllerInterface
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handleRequest(): HTTPRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new InvalidRequestMethodException("Request-Method must be 'POST'.");
        }
        $loginUserId = SessionManager::get('user_id');
        $this->notificationService->confirmAllNotifications($loginUserId);
        return new JSONRenderer(200, []);
    }
}
