<?php

namespace Http\Request;

class GetMessageRequest
{
    private int $messageId;

    public function __construct(array $getData)
    {
        $this->messageId = $getData['message_id'];
    }

    public function getMessageId(): int
    {
        return $this->messageId;
    }
}
