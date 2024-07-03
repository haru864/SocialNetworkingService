<?php

namespace Database\DataAccess\Interfaces;

use Models\Hobby;

interface HobbiesDAO
{
    public function create(Hobby $hobby): Hobby;
    public function getByUserId(int $userId): ?array;
    public function update(Hobby $hobby): bool;
    public function deleteByUserId(int $userId): bool;
}
