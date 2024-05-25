<?php

namespace Database\DataAccess\Interfaces;

use Models\FollowNotification;

interface FollowNotificationDAO
{
    public function create(FollowNotification $followNotification): FollowNotification;
    public function update(FollowNotification $followNotification): FollowNotification;
    public function confirmAllNotification(int $userId): void;
    public function delete(int $id): bool;
}
