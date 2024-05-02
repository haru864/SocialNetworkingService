<?php

namespace Database\DataAccess\Implementations;

use Database\DataAccess\Interfaces\FollowsDAO;
use Database\DatabaseManager;
use Exceptions\InternalServerException;
use Exceptions\QueryFailedException;
use Models\Follow;

class FollowsDAOImpl implements FollowsDAO
{
    public function create(Follow $follow): Follow
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            INSERT INTO follows (
                follower_id,
                followee_id,
                follow_datetime
            )
            VALUES (
                ?, ?, ?
            )
        SQL;
        $result = $mysqli->prepareAndExecute(
            $query,
            'iis',
            [
                $follow->getFollowerId(),
                $follow->getFolloweeId(),
                $follow->getFollowDatetime(),
            ],
        );
        if (!$result) {
            throw new QueryFailedException("INSERT INTO failed.");
        }
        $follow->setId($mysqli->insert_id);
        return $follow;
    }

    public function getFollow(int $followerId, int $followeeId): ?Follow
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $query = <<<SQL
            SELECT * FROM follows
            WHERE follower_id = ? AND followee_id = ?
        SQL;
        $record = $mysqli->prepareAndFetchAll($query, 'ii', [$followerId, $followeeId]);
        return is_null($record) || count($record) === 0 ? null : $this->convertRecordToFollow($record[0]);
    }

    public function getFollowers(int $followeeId, int $limit = null, int $offset = null): ?array
    {
        if (
            (is_null($limit) && !is_null($offset))
            || (!is_null($limit) && is_null($offset))
        ) {
            throw new InternalServerException('limit and offset can be set both or neither.');
        }
        $mysqli = DatabaseManager::getMysqliConnection();
        if (is_null($limit) && is_null($offset)) {
            $query = <<<SQL
                SELECT * FROM follows
                WHERE followee_id = ?
                ORDER BY id DESC
            SQL;
            $records = $mysqli->prepareAndFetchAll($query, 'i', [$followeeId]);
        } else {
            $query = <<<SQL
                SELECT * FROM follows
                WHERE followee_id = ?
                ORDER BY id DESC
                LIMIT ?
                OFFSET ?
            SQL;
            $records = $mysqli->prepareAndFetchAll($query, 'iii', [$followeeId, $limit, $offset]);
        }
        return is_null($records) ? null : $this->convertRecordArrayToFollowArray($records);
    }

    public function getFollowees(int $followerId, int $limit = null, int $offset = null): ?array
    {
        if (
            (is_null($limit) && !is_null($offset))
            || (!is_null($limit) && is_null($offset))
        ) {
            throw new InternalServerException('limit and offset can be set both or neither.');
        }
        $mysqli = DatabaseManager::getMysqliConnection();
        if (is_null($limit) && is_null($offset)) {
            $query = <<<SQL
                SELECT * FROM follows
                WHERE follower_id = ?
                ORDER BY id DESC
            SQL;
            $records = $mysqli->prepareAndFetchAll($query, 'i', [$followerId]);
        } else {
            $query = <<<SQL
                SELECT * FROM follows
                WHERE follower_id = ?
                ORDER BY id DESC
                LIMIT ?
                OFFSET ?
            SQL;
            $records = $mysqli->prepareAndFetchAll($query, 'iii', [$followerId, $limit, $offset]);
        }
        return is_null($records) ? null : $this->convertRecordArrayToFollowArray($records);
    }

    public function delete(int $followerId, int $followeeId): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $sql = "DELETE FROM follows WHERE follower_id = ? AND followee_id = ?";
        return $mysqli->prepareAndExecute($sql, 'ii', [$followerId, $followeeId]);
    }

    private function convertRecordArrayToFollowArray(array $records): array
    {
        $follows = [];
        foreach ($records as $record) {
            $follow = $this->convertRecordToFollow($record);
            array_push($follows, $follow);
        }
        return $follows;
    }

    private function convertRecordToFollow(array $data): Follow
    {
        $follow = new Follow(
            id: $data['id'],
            followerId: $data['follower_id'],
            followeeId: $data['followee_id'],
            followDatetime: $data['follow_datetime']
        );
        return $follow;
    }
}
