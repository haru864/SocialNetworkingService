<?php

namespace Database\DataAccess\Interfaces;

use Models\LikeNotification;

interface LikeNotificationDAO
{
    public function create(LikeNotification $likeNotification): LikeNotification;
    public function update(LikeNotification $likeNotification): LikeNotification;
    public function confirmAllNotification(int $userId): void;
    public function delete(int $id): bool;
}
