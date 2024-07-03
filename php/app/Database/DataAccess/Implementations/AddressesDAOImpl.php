<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\AddressesDAO;
use Database\DatabaseManager;
use Models\Address;
use Exceptions\InvalidDataException;
use Exceptions\QueryFailedException;

class AddressesDAOImpl implements AddressesDAO
{
    public function create(Address $address): Address
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO addresses (
                user_id,
                country,
                state,
                city,
                town
            )
            VALUES (
                ?, ?, ?, ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'issss',
            [
                $address->getUserId(),
                $address->getCountry(),
                $address->getState(),
                $address->getCity(),
                $address->getTown()
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT INTO 'users' failed.");
        }
        $address->setId($mysqli->insert_id);
        return $address;
    }

    public function getByUserId(int $userId): ?Address
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM addresses WHERE user_id = ?";
        $record = $mysqli->prepareAndFetchAll($query, 'i', [$userId])[0] ?? null;
        return $record === null ? null : $this->convertRecordToAddress($record);
    }

    public function update(Address $address): bool
    {
        if ($address->getId() === null) {
            throw new InvalidDataException('Address specified has no ID.');
        }
        $addressInTable = $this->getByUserId($address->getUserId());
        if ($addressInTable === null) {
            throw new InvalidDataException(sprintf("Address's ID '%s' does not exist.", $address->getId()));
        }
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE
                addresses
            SET
                country = ?,
                state = ?,
                city = ?,
                town = ?
            WHERE
                id = ?
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'ssssi',
            [
                $address->getCountry(),
                $address->getState(),
                $address->getCity(),
                $address->getTown(),
                $address->getId()
            ],
        );
        if (!$result) {
            throw new QueryFailedException("UPDATE 'address' failed.");
        }
        return $mysqli->insert_id;
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM addresses WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }

    private function convertRecordArrayToAdressArray(array $records): array
    {
        $addresses = [];
        foreach ($records as $record) {
            $address = $this->convertRecordToAddress($record);
            array_push($addresses, $address);
        }
        return $addresses;
    }

    private function convertRecordToAddress(array $data): Address
    {
        return new Address(
            id: $data['id'],
            userId: $data['user_id'],
            country: $data['country'],
            state: $data['state'],
            city: $data['city'],
            town: $data['town']
        );
    }
}
