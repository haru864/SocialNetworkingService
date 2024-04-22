<?php

namespace Database\DataAccess\Interfaces;

use Models\PendingAddress;

interface PendingAddressesDAO
{
    public function create(PendingAddress $pendingAddress): PendingAddress;
    public function getByUserId(int $pendingUserId): ?PendingAddress;
    public function update(PendingAddress $pendingAddress): bool;
    public function delete(int $id): bool;
}
