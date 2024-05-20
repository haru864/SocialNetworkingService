<?php

namespace Database\DataAccess\Interfaces;

use Models\ReplyNotification;

interface ReplyNotificationDAO
{
    public function create(ReplyNotification $replyNotification): ReplyNotification;
    public function update(ReplyNotification $replyNotification): ReplyNotification;
    public function delete(int $id): bool;
}
