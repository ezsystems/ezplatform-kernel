<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Controller\Content;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Core\Pagination\Pagerfanta\ContentSearchHitAdapter;
use Ibexa\Core\Pagination\Pagerfanta\LocationSearchHitAdapter;
use Ibexa\Core\Pagination\Pagerfanta\Pagerfanta;
use Ibexa\Core\QueryType\ContentViewQueryTypeMapper;
use Pagerfanta\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * A content view controller that runs queries based on the matched view configuration.
 *
 * The action used depends on which type of search is needed: location, content or contentInfo.
 */
class QueryController
{
    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    private $searchService;

    /** @var \Ibexa\Core\QueryType\ContentViewQueryTypeMapper */
    private $contentViewQueryTypeMapper;

    public function __construct(
        ContentViewQueryTypeMapper $contentViewQueryTypeMapper,
        SearchService $searchService
    ) {
        $this->contentViewQueryTypeMapper = $contentViewQueryTypeMapper;
        $this->searchService = $searchService;
    }

    /**
     * Runs a content search.
     *
     * @param \Ibexa\Core\MVC\Symfony\View\ContentView $view
     *
     * @return \Ibexa\Core\MVC\Symfony\View\ContentView
     */
    public function contentQueryAction(ContentView $view)
    {
        $this->runQuery($view, 'findContent');

        return $view;
    }

    /**
     * Runs a location search.
     *
     * @param \Ibexa\Core\MVC\Symfony\View\ContentView $view
     *
     * @return \Ibexa\Core\MVC\Symfony\View\ContentView
     */
    public function locationQueryAction(ContentView $view)
    {
        $this->runQuery($view, 'findLocations');

        return $view;
    }

    /**
     * Runs a contentInfo search.
     *
     * @param \Ibexa\Core\MVC\Symfony\View\ContentView $view
     *
     * @return \Ibexa\Core\MVC\Symfony\View\ContentView
     */
    public function contentInfoQueryAction(ContentView $view)
    {
        $this->runQuery($view, 'findContentInfo');

        return $view;
    }

    /**
     * Runs the Query defined in $view using $method on SearchService.
     *
     * @param \Ibexa\Core\MVC\Symfony\View\ContentView $view
     * @param string $method Name of the SearchService method to run.
     */
    private function runQuery(ContentView $view, $method)
    {
        $searchResults = $this->searchService->$method(
            $this->contentViewQueryTypeMapper->map($view)
        );
        $view->addParameters([$view->getParameter('query')['assign_results_to'] => $searchResults]);
    }

    /**
     * @param \Ibexa\Core\MVC\Symfony\View\ContentView $view
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Ibexa\Core\MVC\Symfony\View\ContentView
     */
    public function pagingQueryAction(ContentView $view, Request $request)
    {
        $this->runPagingQuery($view, $request);

        return $view;
    }

    /**
     * @param \Ibexa\Core\MVC\Symfony\View\ContentView $view
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    private function runPagingQuery(ContentView $view, Request $request)
    {
        $queryParameters = $view->getParameter('query');

        $limit = $queryParameters['limit'] ?? 10;
        $pageParam = $queryParameters['page_param'] ?? 'page';

        $page = $request->get($pageParam, 1);

        $pager = new Pagerfanta(
            $this->getAdapter($this->contentViewQueryTypeMapper->map($view))
        );

        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        $view->addParameters([$queryParameters['assign_results_to'] => $pager]);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query $query
     *
     * @return \Pagerfanta\Adapter\AdapterInterface
     */
    private function getAdapter(Query $query): AdapterInterface
    {
        if ($query instanceof LocationQuery) {
            return new LocationSearchHitAdapter($query, $this->searchService);
        }

        return new ContentSearchHitAdapter($query, $this->searchService);
    }
}

class_alias(QueryController::class, 'eZ\Publish\Core\MVC\Symfony\Controller\Content\QueryController');
