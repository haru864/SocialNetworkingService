<?php

namespace Database\DataAccess\Interfaces;

use Models\MessageNotification;

interface MessageNotificationDAO
{
    public function create(MessageNotification $messageNotification): MessageNotification;
    public function update(MessageNotification $messageNotification): MessageNotification;
    public function confirmAllNotification(int $userId): void;
    public function delete(int $id): bool;
}
