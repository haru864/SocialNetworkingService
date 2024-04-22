<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\PendingAddressesDAO;
use Database\DatabaseManager;
use Models\Address;
use Exceptions\InvalidDataException;
use Exceptions\QueryFailedException;
use Models\PendingAddress;

class PendingAddressesDAOImpl implements PendingAddressesDAO
{
    public function create(PendingAddress $pendingAddress): PendingAddress
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO pending_addresses (
                pending_user_id,
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
                $pendingAddress->getPendingUserId(),
                $pendingAddress->getCountry(),
                $pendingAddress->getState(),
                $pendingAddress->getCity(),
                $pendingAddress->getTown()
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT failed.");
        }
        $pendingAddress->setId($mysqli->insert_id);
        return $pendingAddress;
    }

    public function getByUserId(int $pendingUserId): ?PendingAddress
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM pending_addresses WHERE pending_user_id = ?";
        $record = $mysqli->prepareAndFetchAll($query, 'i', [$pendingUserId])[0] ?? null;
        return $record === null ? null : $this->convertRecordToPendingAddress($record);
    }

    public function update(PendingAddress $pendingAddress): bool
    {
        if ($pendingAddress->getId() === null) {
            throw new InvalidDataException('PendingAddress specified has no ID.');
        }
        $pendingAddressInTable = $this->getByUserId($pendingAddress->getPendingUserId());
        if ($pendingAddressInTable === null) {
            throw new InvalidDataException(sprintf("PendingAddress's ID '%s' does not exist.", $pendingAddress->getId()));
        }
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE
                pending_addresses
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
                $pendingAddress->getCountry(),
                $pendingAddress->getState(),
                $pendingAddress->getCity(),
                $pendingAddress->getTown(),
                $pendingAddress->getId()
            ],
        );
        if (!$result) {
            throw new QueryFailedException("UPDATE failed.");
        }
        return $mysqli->insert_id;
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM pending_addresses WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }

    private function convertRecordArrayToPendingAdressArray(array $records): array
    {
        $pendingAddresses = [];
        foreach ($records as $record) {
            $pendingAddress = $this->convertRecordToPendingAddress($record);
            array_push($pendingAddresses, $pendingAddress);
        }
        return $pendingAddresses;
    }

    private function convertRecordToPendingAddress(array $data): PendingAddress
    {
        return new PendingAddress(
            id: $data['id'],
            pendingUserId: $data['pending_user_id'],
            country: $data['country'],
            state: $data['state'],
            city: $data['city'],
            town: $data['town']
        );
    }
}
