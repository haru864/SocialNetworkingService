<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\HobbiesDAO;
use Database\DatabaseManager;
use Models\Career;
use Exceptions\InvalidDataException;
use Exceptions\QueryFailedException;
use Models\Hobby;

class HobbiesDAOImpl implements HobbiesDAO
{
    public function create(Hobby $hobby): Hobby
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO hobbies (
                user_id, hobby
            )
            VALUES (
                ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'is',
            [
                $hobby->getUserId(),
                $hobby->getHobby(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT INTO 'hobbies' failed.");
        }
        $hobby->setId($mysqli->insert_id);
        return $hobby;
    }

    public function getByUserId(int $userId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM hobbies WHERE user_id = ?";
        $records = $mysqli->prepareAndFetchAll($query, 'i', [$userId]);
        return $records === null ? null : $this->convertRecordArrayToHobbyArray($records);
    }

    public function update(Hobby $hobby): bool
    {
        if ($hobby->getId() === null) {
            throw new InvalidDataException('Hobby specified has no ID.');
        }
        $hobbiesInTable = $this->getByUserId($hobby->getUserId());
        if ($hobbiesInTable === null) {
            throw new InvalidDataException(sprintf("Hobby's ID '%s' does not exist.", $hobby->getId()));
        }
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE
                hobbies
            SET
                hobby = ?
            WHERE
                id = ?
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'si',
            [
                $hobby->getHobby(),
                $hobby->getId()
            ],
        );
        if (!$result) {
            throw new QueryFailedException("UPDATE 'hobbies' failed.");
        }
        return $mysqli->insert_id;
    }

    public function deleteByUserId(int $userId): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM hobbies WHERE user_id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$userId]);
    }

    private function convertRecordArrayToHobbyArray(array $records): array
    {
        $hobbies = [];
        foreach ($records as $record) {
            $hobby = $this->convertRecordToHobby($record);
            array_push($hobbies, $hobby);
        }
        return $hobbies;
    }

    private function convertRecordToHobby(array $data): Hobby
    {
        return new Hobby(
            id: $data['id'],
            userId: $data['user_id'],
            hobby: $data['hobby']
        );
    }
}
