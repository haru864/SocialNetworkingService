<?php

namespace Services;

use Models\Message;
use Settings\Settings;

class LiveMessageService
{
    public function __construct()
    {
    }

    private function postMessageToSseEndpoint(string $data_json)
    {
        $url = Settings::env('SSE_MESSAGE_URL');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_json)
        ));
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $errMsg = 'SSE Post Error: ' . curl_error($ch);
            throw new \Exception($errMsg);
        }

        curl_close($ch);
    }

    public function publishMessage(Message $message): void
    {
        $msg = json_encode([
            'id' => $message->getId(),
            'senderId' => $message->getSenderId(),
            'recipientId' => $message->getRecipientId(),
            'message' => $message->getMessage(),
            'mediaFileName' => $message->getMediaFileName(),
            'mediaType' => $message->getMediaType(),
            'sendDatetime' => $message->getSendDatetime()
        ]);
        $this->postMessageToSseEndpoint($msg);
    }
}
