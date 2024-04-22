<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\PendingCareersDAO;
use Database\DatabaseManager;
use Models\Career;
use Exceptions\InvalidDataException;
use Exceptions\QueryFailedException;
use Models\PendingCareer;

class PendingCareersDAOImpl implements PendingCareersDAO
{
    public function create(PendingCareer $pendingCareer): PendingCareer
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO pending_careers (
                pending_user_id, job
            )
            VALUES (
                ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'is',
            [
                $pendingCareer->getPendingUserId(),
                $pendingCareer->getJob(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT failed.");
        }
        $pendingCareer->setId($mysqli->insert_id);
        return $pendingCareer;
    }

    public function getByUserId(int $pendingUserId): ?array
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM pending_careers WHERE pending_user_id = ?";
        $records = $mysqli->prepareAndFetchAll($query, 'i', [$pendingUserId]);
        return $records === null ? null : $this->convertRecordArrayToPendingCareerArray($records);
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM pending_careers WHERE id = ?";
        return $mysqli->prepareAndExecute($sql, 'i', [$id]);
    }

    private function convertRecordArrayToPendingCareerArray(array $records): array
    {
        $pendingCareers = [];
        foreach ($records as $record) {
            $pendingCareer = $this->convertRecordArrayToPendingCareer($record);
            array_push($pendingCareers, $pendingCareer);
        }
        return $pendingCareers;
    }

    private function convertRecordArrayToPendingCareer(array $data): PendingCareer
    {
        return new PendingCareer(
            id: $data['id'],
            pendingUserId: $data['pending_user_id'],
            job: $data['job']
        );
    }
}
