<?php

namespace Services;

use Database\DataAccess\Implementations\TweetsDAOImpl;
use Database\DataAccess\Implementations\UsersDAOImpl;

class SearchService
{
    private UsersDAOImpl $usersDAOImpl;
    private TweetsDAOImpl $tweetsDAOImpl;

    public function __construct(UsersDAOImpl $usersDAOImpl, TweetsDAOImpl $tweetsDAOImpl)
    {
        $this->usersDAOImpl = $usersDAOImpl;
        $this->tweetsDAOImpl = $tweetsDAOImpl;
    }

    public function getUsersByNameKeyword(string $keyword, int $limit, int $offset): array
    {
        $users = $this->usersDAOImpl->getByPartialNameMatch($keyword, $limit, $offset);
        $userArrs = [];
        foreach ($users as $user) {
            $userArr = $user->toArray();
            $userArr['email'] = "";
            array_push($userArrs, $userArr);
        }
        return $userArrs;
    }

    public function getUsersByAddressKeyword(string $keyword, int $limit, int $offset): array
    {
        $users = $this->usersDAOImpl->getByPartialAddressMatch($keyword, $limit, $offset);
        $userArrs = [];
        foreach ($users as $user) {
            $userArr = $user->toArray();
            $userArr['email'] = "";
            array_push($userArrs, $userArr);
        }
        return $userArrs;
    }

    public function getUsersByJobKeyword(string $keyword, int $limit, int $offset): array
    {
        $users = $this->usersDAOImpl->getByPartialJobMatch($keyword, $limit, $offset);
        $userArrs = [];
        foreach ($users as $user) {
            $userArr = $user->toArray();
            $userArr['email'] = "";
            array_push($userArrs, $userArr);
        }
        return $userArrs;
    }

    public function getTweetsByKeyword(string $keyword, int $limit, int $offset): array
    {
        $tweets = $this->tweetsDAOImpl->getByKeywordMatch($keyword, $limit, $offset);
        return $tweets;
    }
}
