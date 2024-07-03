<?php

namespace Database\DataAccess\Interfaces;

use Models\User;

interface UsersDAO
{
    public function create(User $user): User;
    public function getById(int $id): ?User;
    public function getByName(string $name): ?User;
    public function getByPartialNameMatch(string $name, int $limit, int $offset): array;
    public function getByPartialAddressMatch(string $address, int $limit, int $offset): array;
    public function getByPartialJobMatch(string $job, int $limit, int $offset): array;
    public function getByPartialHobbyMatch(string $job, int $limit, int $offset): array;
    public function update(User $user): bool;
    public function delete(int $id): bool;
}
