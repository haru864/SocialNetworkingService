<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
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
        
        return new JSONRenderer(200, []);
    }
}
