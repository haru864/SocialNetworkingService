<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Http\Request\SearchTweetsRequest;
use Services\SearchService;

class SearchTweetsController implements ControllerInterface
{
    private SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function handleRequest(): JSONRenderer
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            throw new InvalidRequestMethodException("Request method must be 'GET'.");
        }
        $request = new SearchTweetsRequest($_GET);
        return $this->searchTweetsBtKeyword(
            keyword: $request->getQuery(),
            page: $request->getPage(),
            limit: $request->getLimit()
        );
    }

    private function searchTweetsBtKeyword(string $keyword, int $page, int $limit): JSONRenderer
    {
        $offset = ($page - 1) * $limit;
        $tweets = $this->searchService->getTweetsByKeyword(
            keyword: $keyword,
            limit: $limit,
            offset: $offset
        );
        $tweetArr = [];
        foreach ($tweets as $tweet) {
            array_push($tweetArr, $tweet->toArray());
        }
        return new JSONRenderer(200, ['tweets' => $tweetArr]);
    }
}
