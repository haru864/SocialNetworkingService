<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\CareersDAO;
use Database\DatabaseManager;
use Models\Career;
use Exceptions\InvalidDataException;
use Exceptions\QueryFailedException;

class CareersDAOImpl implements CareersDAO
{
    public function create(Career $career): Career
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO careers (
                user_id, job
            )
            VALUES (
                ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'is',
            [
                $career->getUserId(),
                $career->getJob(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT INTO 'careers' failed.");
        }
        $career->setId($mysqli->insert_id);
        return $career;
    }

    public function getByUserId(int $userId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM careers WHERE user_id = ?";
        $records = $mysqli->prepareAndFetchAll($query, 'i', [$userId]);
        return $records === null ? null : $this->convertRecordArrayToCareerArray($records);
    }

    public function update(Career $career): bool
    {
        if ($career->getId() === null) {
            throw new InvalidDataException('Career specified has no ID.');
        }
        $careersInTable = $this->getByUserId($career->getUserId());
        if ($careersInTable === null) {
            throw new InvalidDataException(sprintf("Career's ID '%s' does not exist.", $career->getId()));
        }
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            UPDATE
                careers
            SET
                job = ?
            WHERE
                id = ?
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'si',
            [
                $career->getJob(),
                $career->getId()
            ],
        );
        if (!$result) {
            throw new QueryFailedException("UPDATE 'careers' failed.");
        }
        return $mysqli->insert_id;
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM careers WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }

    private function convertRecordArrayToCareerArray(array $records): array
    {
        $careers = [];
        foreach ($records as $record) {
            $career = $this->convertRecordToCareer($record);
            array_push($careers, $career);
        }
        return $careers;
    }

    private function convertRecordToCareer(array $data): Career
    {
        return new Career(
            id: $data['id'],
            userId: $data['user_id'],
            job: $data['job']
        );
    }
}
