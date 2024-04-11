<?php

namespace Database\DataAccess\Interfaces;

use Models\Message;

interface MessagesDAO
{
    public function create(Message $message): Message;
    public function getSenders(int $userId): ?array;
    public function getRecipients(int $userId): ?array;
    public function getMessageExchanges(int $yourUserId, int $otherUserId, int $limit, int $offset): ?array;
    public function deleteMessageExchanges(int $yourUserId, int $otherUserId): bool;
}
