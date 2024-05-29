<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\UsersDAO;
use Database\DatabaseManager;
use Models\User;
use Exceptions\InvalidDataException;
use Exceptions\QueryFailedException;

class UsersDAOImpl implements UsersDAO
{
    public function create(User $user): User
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO users (
                name,
                password_hash,
                email,
                self_introduction,
                profile_image,
                created_at,
                last_login
            )
            VALUES (
                ?, ?, ?, ?, ?, ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'sssssss',
            [
                $user->getName(),
                $user->getPasswordHash(),
                $user->getEmail(),
                $user->getSelfIntroduction(),
                $user->getProfileImage(),
                $user->getCreatedAt(),
                $user->getLastLogin()
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT failed.");
        }
        $user->setId($mysqli->insert_id);
        return $user;
    }

    public function getById(int $id): ?User
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM users WHERE id = ?";
        $record = $mysqli->prepareAndFetchAll($query, 'i', [$id])[0] ?? null;
        return $record === null ? null : $this->convertRecordToUser($record);
    }

    public function getByName(string $name): ?User
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM users WHERE name = ?";
        $record = $mysqli->prepareAndFetchAll($query, 's', [$name])[0] ?? null;
        return $record === null ? null : $this->convertRecordToUser($record);
    }

    public function getByPartialNameMatch(string $name, int $limit, int $offset): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $likeTerm = "%" . $name . "%";
        $query = <<<SQL
            SELECT
                *
            FROM
                users
            WHERE
                name LIKE ?
            ORDER BY id DESC
            LIMIT ?
            OFFSET ?
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'sii', [$likeTerm, $limit, $offset]);
        return $records === null ? [] : $this->convertRecordArrayToUserArray($records);
    }

    public function getByPartialAddressMatch(string $address, int $limit, int $offset): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $likeTerm = "%" . $address . "%";
        $query = <<<SQL
            SELECT
                *
            FROM
                users
            INNER JOIN (
                SELECT DISTINCT
                    user_id
                FROM
                    addresses
                WHERE
                    country LIKE ?
                    OR state LIKE ?
                    OR city LIKE ?
                    OR town LIKE ?
            ) addr
            ON
                users.id = addr.user_id
            ORDER BY id DESC
            LIMIT ?
            OFFSET ?
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'ssssii', [$likeTerm, $likeTerm, $likeTerm, $likeTerm, $limit, $offset]);
        return $records === null ? [] : $this->convertRecordArrayToUserArray($records);
    }

    public function getByPartialJobMatch(string $job, int $limit, int $offset): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $likeTerm = "%" . $job . "%";
        $query = <<<SQL
            SELECT
                *
            FROM
                users
            INNER JOIN (
                SELECT DISTINCT
                    user_id
                FROM
                    careers
                WHERE
                    job LIKE ?
            ) j
            ON
                users.id = j.user_id
            ORDER BY id DESC
            LIMIT ?
            OFFSET ?
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'sii', [$likeTerm, $limit, $offset]);
        return $records === null ? [] : $this->convertRecordArrayToUserArray($records);
    }

    public function getByPartialHobbyMatch(string $hobby, int $limit, int $offset): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $likeTerm = "%" . $hobby . "%";
        $query = <<<SQL
            SELECT
                *
            FROM
                users
            INNER JOIN (
                SELECT DISTINCT
                    user_id
                FROM
                    hobbies
                WHERE
                    hobby LIKE ?
            ) h
            ON
                users.id = h.user_id
            ORDER BY id DESC
            LIMIT ?
            OFFSET ?
        SQL;
        $records = $mysqli->prepareAndFetchAll($query, 'sii', [$likeTerm, $limit, $offset]);
        return $records === null ? [] : $this->convertRecordArrayToUserArray($records);
    }

    public function update(User $user): bool
    {
        if ($user->getId() === null) {
            throw new InvalidDataException('User specified has no ID.');
        }
        $userInTable = $this->getById($user->getId());
        if ($userInTable === null) {
            throw new InvalidDataException(sprintf("User's ID '%s' does not exist.", $user->getId()));
        }
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE
                users
            SET
                name = ?,
                password_hash = ?,
                email = ?,
                self_introduction = ?,
                profile_image = ?,
                created_at = ?,
                last_login = ?
            WHERE
                id = ?
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'sssssssi',
            [
                $user->getName(),
                $user->getPasswordHash(),
                $user->getEmail(),
                $user->getSelfIntroduction(),
                $user->getProfileImage(),
                $user->getCreatedAt(),
                $user->getLastLogin(),
                $user->getId()
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
        $sql = "DELETE FROM users WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }

    private function convertRecordArrayToUserArray(array $records): array
    {
        $users = [];
        foreach ($records as $record) {
            $user = $this->convertRecordToUser($record);
            array_push($users, $user);
        }
        return $users;
    }

    private function convertRecordToUser(array $data): User
    {
        return new User(
            id: $data['id'],
            name: $data['name'],
            password_hash: $data['password_hash'],
            email: $data['email'],
            self_introduction: $data['self_introduction'],
            profile_image: $data['profile_image'],
            created_at: $data['created_at'],
            last_login: $data['last_login'],
        );
    }
}
