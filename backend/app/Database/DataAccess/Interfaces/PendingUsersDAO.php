<?php

namespace Database\DataAccess\Interfaces;

use Models\PendingUser;

interface PendingUsersDAO
{
    public function create(PendingUser $pendingUser): PendingUser;
    public function getById(int $id): ?PendingUser;
    public function getByName(string $name): ?PendingUser;
    public function update(PendingUser $pendingUser): bool;
    public function delete(int $id): bool;
}
