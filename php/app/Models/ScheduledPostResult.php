<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class ScheduledPostResult implements Model
{
    use GenericModel;

    private int $scheduledTweetCount;
    private int $postedTweetCount;
    private array $scheduledPostError;

    public function __construct(
        int $scheduledTweetCount,
        int $postedTweetCount,
        array $scheduledPostError
    ) {
        $this->scheduledTweetCount = $scheduledTweetCount;
        $this->postedTweetCount = $postedTweetCount;
        $this->scheduledPostError = $scheduledPostError;
    }

    public function getScheduledTweetCount(): int
    {
        return $this->scheduledTweetCount;
    }

    public function getPostedTweetCount(): int
    {
        return $this->postedTweetCount;
    }

    public function getScheduledPostError(): array
    {
        return $this->scheduledPostError;
    }

    public function toArray(): array
    {
        $data = [
            'scheduledTweetCount' => $this->getScheduledTweetCount(),
            'postedTweetCount' => $this->getPostedTweetCount(),
            'scheduledPostError' => json_encode($this->scheduledPostError)
        ];
        return $data;
    }
}
