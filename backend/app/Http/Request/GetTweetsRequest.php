<?php

namespace Http\Request;

use Exceptions\InvalidRequestParameterException;
use Helpers\ValidationHelper;

class GetTweetsRequest
{
    private string $type;
    private ?int $userId;
    private ?int $tweetId;
    private ?int $page;
    private ?int $limit;

    public function __construct(array $getData)
    {
        $this->type = $getData['type'];
        $validTypes = ['trend', 'follower', 'user', 'tweet'];
        if (!in_array($this->type, $validTypes)) {
            $validTypeStr = implode(",", $validTypes);
            throw new InvalidRequestParameterException("'type' must be in [{$validTypeStr}].");
        }

        if (in_array($this->type, ['trend', 'follower', 'user'])) {
            $requiredParams = ['type', 'page', 'limit'];
        } else if ($this->type === 'tweet') {
            $requiredParams = ['type', 'tweet_id'];
        }
        foreach ($requiredParams as $param) {
            if (is_null($getData[$param])) {
                throw new InvalidRequestParameterException("'{$param}' must be set in get-tweets request.");
            }
        }

        if (in_array($this->type, ['trend', 'follower', 'user'])) {
            if (
                !ValidationHelper::isPositiveIntegerString($getData['page'])
                || !ValidationHelper::isPositiveIntegerString($getData['limit'])
            ) {
                throw new InvalidRequestParameterException("'page' and 'limit' must be positive integer string.");
            }
        }

        if ($this->type === 'user' && isset($getData['user_id']) && !ValidationHelper::isPositiveIntegerString($getData['user_id'])) {
            throw new InvalidRequestParameterException("'user_id' must be positive integer string.");
        }

        if ($this->type === 'tweet' && !ValidationHelper::isPositiveIntegerString($getData['tweet_id'])) {
            throw new InvalidRequestParameterException("'tweet_id' must be positive integer string.");
        }

        $this->userId = $getData['user_id'];
        $this->tweetId = $getData['tweet_id'];
        $this->page = $getData['page'];
        $this->limit = $getData['limit'];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getTweetId(): ?int
    {
        return $this->tweetId;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }
}
