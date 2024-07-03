<?php

namespace Database\DataAccess\Interfaces;

use Models\PendingHobby;

interface PendingHobbiesDAO
{
    public function create(PendingHobby $pendingHobby): PendingHobby;
    public function getByUserId(int $pendingUserId): ?array;
    public function delete(int $id): bool;
}
