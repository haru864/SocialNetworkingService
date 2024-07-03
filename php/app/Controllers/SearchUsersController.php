<?php

namespace Controllers;

use Controllers\Interface\ControllerInterface;
use Render\JSONRenderer;
use Exceptions\InvalidRequestMethodException;
use Exceptions\InvalidRequestParameterException;
use Http\Request\SearchUsersRequest;
use Services\SearchService;

class SearchUsersController implements ControllerInterface
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
        $request = new SearchUsersRequest($_GET);
        switch ($request->getField()) {
            case 'name':
                return $this->searchUsersByNameMatching(
                    $request->getQuery(),
                    $request->getPage(),
                    $request->getLimit()
                );
                break;
            case 'address':
                return $this->searchUsersByAddressMatching(
                    $request->getQuery(),
                    $request->getPage(),
                    $request->getLimit()
                );
                break;
            case 'job':
                return $this->searchUsersByJobMatching(
                    $request->getQuery(),
                    $request->getPage(),
                    $request->getLimit()
                );
                break;
            case 'hobby':
                return $this->searchUsersByHobbyMatching(
                    $request->getQuery(),
                    $request->getPage(),
                    $request->getLimit()
                );
                break;
            default:
                throw new InvalidRequestParameterException("Invalid parameter at 'field'.");
                break;
        }
    }

    private function searchUsersByNameMatching(string $keyword, int $page, int $limit): JSONRenderer
    {
        $offset = ($page - 1) * $limit;
        $users = $this->searchService->getUsersByNameKeyword(
            keyword: $keyword,
            limit: $limit,
            offset: $offset
        );
        return new JSONRenderer(200, ['users' => $users]);
    }

    private function searchUsersByAddressMatching(string $keyword, int $page, int $limit): JSONRenderer
    {
        $offset = ($page - 1) * $limit;
        $users = $this->searchService->getUsersByAddressKeyword(
            keyword: $keyword,
            limit: $limit,
            offset: $offset
        );
        return new JSONRenderer(200, ['users' => $users]);
    }

    private function searchUsersByJobMatching(string $keyword, int $page, int $limit): JSONRenderer
    {
        $offset = ($page - 1) * $limit;
        $users = $this->searchService->getUsersByJobKeyword(
            keyword: $keyword,
            limit: $limit,
            offset: $offset
        );
        return new JSONRenderer(200, ['users' => $users]);
    }

    private function searchUsersByHobbyMatching(string $keyword, int $page, int $limit): JSONRenderer
    {
        $offset = ($page - 1) * $limit;
        $users = $this->searchService->getUsersByHobbyKeyword(
            keyword: $keyword,
            limit: $limit,
            offset: $offset
        );
        return new JSONRenderer(200, ['users' => $users]);
    }
}
