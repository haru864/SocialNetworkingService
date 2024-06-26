<?php

namespace Database\DataAccess\Interfaces;

use Models\RetweetNotification;

interface RetweetNotificationDAO
{
    public function create(RetweetNotification $retweetNotification): RetweetNotification;
    public function update(RetweetNotification $retweetNotification): RetweetNotification;
    public function confirmAllNotification(int $userId): void;
    public function delete(int $id): bool;
}
