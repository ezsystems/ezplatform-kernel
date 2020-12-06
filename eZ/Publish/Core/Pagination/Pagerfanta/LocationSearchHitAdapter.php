<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

/**
 * Pagerfanta adapter for eZ Publish location search.
 * Will return results as SearchHit objects.
 */
class LocationSearchHitAdapter extends AbstractSearchResultAdapter
{
    public function __construct(LocationQuery $query, SearchService $searchService, array $languageFilter = [])
    {
        parent::__construct($query, $searchService, $languageFilter);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     */
    protected function executeQuery(SearchService $searchService, Query $query, array $languageFilter): SearchResult
    {
        return $searchService->findLocations($query, $languageFilter);
    }
}
