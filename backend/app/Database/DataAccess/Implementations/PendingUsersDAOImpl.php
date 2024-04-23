<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\PendingUsersDAO;
use Database\DatabaseManager;
use Exceptions\InvalidDataException;
use Exceptions\QueryFailedException;
use Models\PendingUser;

class PendingUsersDAOImpl implements PendingUsersDAO
{
    public function create(PendingUser $pendingUser): PendingUser
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO pending_users (
                user_id,
                name,
                password_hash,
                email,
                self_introduction,
                profile_image
            )
            VALUES (
                ?, ?, ?, ?, ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'isssss',
            [
                $pendingUser->getUserId(),
                $pendingUser->getName(),
                $pendingUser->getPasswordHash(),
                $pendingUser->getEmail(),
                $pendingUser->getSelfIntroduction(),
                $pendingUser->getProfileImage()
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT failed.");
        }
        $pendingUser->setId($mysqli->insert_id);
        return $pendingUser;
    }

    public function getById(int $id): ?PendingUser
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM pending_users WHERE id = ?";
        $record = $mysqli->prepareAndFetchAll($query, 'i', [$id])[0] ?? null;
        return $record === null ? null : $this->convertRecordToPendingUser($record);
    }

    public function getByName(string $name): ?PendingUser
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM pending_users WHERE name = ?";
        $record = $mysqli->prepareAndFetchAll($query, 's', [$name])[0] ?? null;
        return $record === null ? null : $this->convertRecordToPendingUser($record);
    }

    public function update(PendingUser $pendingUser): bool
    {
        if ($pendingUser->getId() === null) {
            throw new InvalidDataException('PendingUser specified has no ID.');
        }
        $pendingUserInTable = $this->getByName($pendingUser->getName());
        if ($pendingUserInTable === null) {
            throw new InvalidDataException(sprintf("PendingUser's ID '%s' does not exist.", $pendingUser->getId()));
        }
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE
                pending_users
            SET
                user_id = ?,
                name = ?,
                password_hash = ?,
                email = ?,
                self_introduction = ?,
                profile_image = ?
            WHERE
                id = ?
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'isssssi',
            [
                $pendingUser->getUserId(),
                $pendingUser->getName(),
                $pendingUser->getPasswordHash(),
                $pendingUser->getEmail(),
                $pendingUser->getSelfIntroduction(),
                $pendingUser->getProfileImage(),
                $pendingUser->getId()
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
        $sql = "DELETE FROM pending_users WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }

    private function convertRecordArrayToPendingUserArray(array $records): array
    {
        $pendingUsers = [];
        foreach ($records as $record) {
            $pendingUser = $this->convertRecordToPendingUser($record);
            array_push($pendingUsers, $pendingUser);
        }
        return $pendingUsers;
    }

    private function convertRecordToPendingUser(array $data): PendingUser
    {
        return new PendingUser(
            id: $data['id'],
            userId: $data['user_id'],
            name: $data['name'],
            password_hash: $data['password_hash'],
            email: $data['email'],
            self_introduction: $data['self_introduction'],
            profile_image: $data['profile_image']
        );
    }
}
