<?php

namespace Database\DataAccess\Interfaces;

use Models\User;

interface UsersDAO
{
    public function create(User $user): User;
    public function getById(int $id): ?User;
    public function getByName(string $name): ?User;
    public function update(User $user): bool;
    public function delete(int $id): bool;
}
