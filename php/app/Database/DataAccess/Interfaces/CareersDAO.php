<?php

namespace Database\DataAccess\Interfaces;

use Models\Career;

interface CareersDAO
{
    public function create(Career $career): Career;
    public function getByUserId(int $userId): ?array;
    public function update(Career $career): bool;
    public function deleteByUserId(int $userId): bool;
}
