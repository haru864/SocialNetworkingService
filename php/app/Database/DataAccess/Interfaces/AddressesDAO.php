<?php

namespace Database\DataAccess\Interfaces;

use Models\Address;

interface AddressesDAO
{
    public function create(Address $address): Address;
    public function getByUserId(int $userId): ?Address;
    public function update(Address $address): bool;
    public function delete(int $id): bool;
}
