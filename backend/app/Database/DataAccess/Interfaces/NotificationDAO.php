<?php

namespace Database\DataAccess\Interfaces;

interface NotificationDAO
{
    public function getAllNotificationsSorted(int $userId): ?array;
}
