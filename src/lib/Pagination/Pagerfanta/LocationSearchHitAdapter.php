<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

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
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery $query
     */
    protected function executeQuery(SearchService $searchService, Query $query, array $languageFilter): SearchResult
    {
        return $searchService->findLocations($query, $languageFilter);
    }
}

class_alias(LocationSearchHitAdapter::class, 'eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchHitAdapter');
