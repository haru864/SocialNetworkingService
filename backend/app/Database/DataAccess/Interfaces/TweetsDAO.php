<?php

namespace Database\DataAccess\Interfaces;

use Models\Tweet;

interface TweetsDAO
{
    public function create(Tweet $tweet): Tweet;
    public function getByUserId(int $userId): ?array;
    public function update(Tweet $tweet): bool;
    public function deleteById(int $id): bool;
}
