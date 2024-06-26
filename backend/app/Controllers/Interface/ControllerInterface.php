<?php

namespace Controllers\Interface;

use Render\Interface\HTTPRenderer;

interface ControllerInterface
{
    public function handleRequest(): HTTPRenderer;
}
