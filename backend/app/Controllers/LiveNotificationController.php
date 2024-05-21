<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\Interface\HTTPRenderer;
use Render\JSONRenderer;
use Services\LiveNotificationService;

class LiveNotificationController implements ControllerInterface
{
    private LiveNotificationService $liveNotificationService;

    public function __construct(LiveNotificationService $liveNotificationService)
    {
        $this->liveNotificationService = $liveNotificationService;
    }

    public function handleRequest(): HTTPRenderer
    {
        
        return new JSONRenderer(200, []);
    }
}
