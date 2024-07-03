<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Http\Request\LiveMessageRequest;
use Render\Interface\HTTPRenderer;
use Render\JSONRenderer;
use Services\LiveMessageService;

class LiveMessageController implements ControllerInterface
{
    private LiveMessageService $liveMessageService;

    public function __construct(LiveMessageService $liveMessageService)
    {
        $this->liveMessageService = $liveMessageService;
    }

    public function handleRequest(): HTTPRenderer
    {
        $request = new LiveMessageRequest();
        $this->liveMessageService->streamMessages($request->getMessagePartnerId());
        return new JSONRenderer(200, []);
    }
}
