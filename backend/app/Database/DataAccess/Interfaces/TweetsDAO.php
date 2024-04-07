<?php

namespace Database\DataAccess\Interfaces;

use Models\Tweet;

interface TweetsDAO
{
    public function create(Tweet $tweet): Tweet;
    public function getByTweetId(int $id): ?Tweet;
    public function getByReplyToId(int $replyToId, int $limit, int $offset): ?array;
    public function getByUserId(int $userId, int $limit, int $offset): ?array;
    public function getByFollower(int $userId, int $limit, int $offset): ?array;
    // TODO リプライ、リツイート実装後に実装する
    // public function getByPopular(int $userId, int $limit, int $offset): ?array;
    public function deleteById(int $id): bool;
}
