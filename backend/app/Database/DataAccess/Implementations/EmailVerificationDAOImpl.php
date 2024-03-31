<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\EmailVerificationDAO;
use Database\DatabaseManager;
use Models\EmailVerification;
use Exceptions\QueryFailedException;

class EmailVerificationDAOImpl implements EmailVerificationDAO
{
    public function create(EmailVerification $emailVerification): void
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO email_verification (
                hash,
                user_id,
                created_at,
                expired_at
            )
            VALUES (
                ?, ?, ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'siss',
            [
                $emailVerification->getHash(),
                $emailVerification->getUserId(),
                $emailVerification->getCreatedAt(),
                $emailVerification->getExpiredAt()
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT INTO 'email_verification' failed.");
        }
        return;
    }

    public function getByHash(string $hash): ?EmailVerification
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = "SELECT * FROM email_verification WHERE hash = ?";
        $record = $mysqli->prepareAndFetchAll($query, 's', [$hash])[0] ?? null;
        return $record === null ? null : $this->convertRecordToEmailVerification($record);
    }

    public function deleteByHash(string $hash): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM email_verification WHERE hash = ?";
        return $mysqli->prepareAndExecute($sql, 's', [$hash]);
    }

    public function deleteExpiredHash(): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM email_verification WHERE expired_at <= NOW()";
        return $mysqli->prepareAndExecute($sql, '', []);
    }

    private function convertRecordArrayToAdressArray(array $records): array
    {
        $emailVerifications = [];
        foreach ($records as $record) {
            $emailVerification = $this->convertRecordToEmailVerification($record);
            array_push($emailVerifications, $emailVerification);
        }
        return $emailVerifications;
    }

    private function convertRecordToEmailVerification(array $data): EmailVerification
    {
        return new EmailVerification(
            hash: $data['hash'],
            userId: $data['user_id'],
            createdAt: $data['created_at'],
            expiredAt: $data['expired_at']
        );
    }
}
