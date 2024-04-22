<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\PendingHobbiesDAO;
use Database\DatabaseManager;
use Exceptions\QueryFailedException;
use Models\Hobby;
use Models\PendingHobby;

class PendingHobbiesDAOImpl implements PendingHobbiesDAO
{
    public function create(PendingHobby $pendingHobby): PendingHobby
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO pending_hobbies (
                pending_user_id, hobby
            )
            VALUES (
                ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'is',
            [
                $pendingHobby->getPendingUserId(),
                $pendingHobby->getHobby(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT failed.");
        }
        $pendingHobby->setId($mysqli->insert_id);
        return $pendingHobby;
    }

    public function getByUserId(int $pendingUserId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM pending_hobbies WHERE pending_user_id = ?";
        $records = $mysqli->prepareAndFetchAll($query, 'i', [$pendingUserId]);
        return $records === null ? null : $this->convertRecordArrayToPendingHobbyArray($records);
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM pending_hobbies WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }

    private function convertRecordArrayToPendingHobbyArray(array $records): array
    {
        $pendingHobbies = [];
        foreach ($records as $record) {
            $pendingHobby = $this->convertRecordToPendingHobby($record);
            array_push($pendingHobbies, $pendingHobby);
        }
        return $pendingHobbies;
    }

    private function convertRecordToPendingHobby(array $data): PendingHobby
    {
        return new PendingHobby(
            id: $data['id'],
            pendingUserId: $data['pending_user_id'],
            hobby: $data['hobby']
        );
    }
}
