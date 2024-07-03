<?php

namespace Database\DataAccess\Interfaces;

use Models\PendingCareer;

interface PendingCareersDAO
{
    public function create(PendingCareer $pendingCareer): PendingCareer;
    public function getByUserId(int $pendingUserId): ?array;
    public function delete(int $id): bool;
}
