<?php

namespace Database\DataAccess\Interfaces;

use Models\ScheduledTweet;

interface ScheduledTweetsDAO
{
    public function create(ScheduledTweet $scheduledTweet): ScheduledTweet;
    public function getByScheduled(string $datetime): array;
    public function deleteById(int $id): bool;
}
