<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Pagination\Pagerfanta\AdapterFactory;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Core\Pagination\Pagerfanta\ContentSearchHitAdapter;
use Ibexa\Core\Pagination\Pagerfanta\FixedSearchResultHitAdapter;
use Ibexa\Core\Pagination\Pagerfanta\LocationSearchHitAdapter;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @internal
 */
final class SearchHitAdapterFactory implements SearchHitAdapterFactoryInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    private $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function createAdapter(Query $query, array $languageFilter = []): AdapterInterface
    {
        if ($query instanceof LocationQuery) {
            return new LocationSearchHitAdapter($query, $this->searchService, $languageFilter);
        }

        return new ContentSearchHitAdapter($query, $this->searchService, $languageFilter);
    }

    public function createFixedAdapter(Query $query, array $languageFilter = []): AdapterInterface
    {
        if ($query instanceof LocationQuery) {
            $searchResults = $this->searchService->findLocations($query, $languageFilter);
        } else {
            $searchResults = $this->searchService->findContent($query, $languageFilter);
        }

        return new FixedSearchResultHitAdapter($searchResults);
    }
}

class_alias(SearchHitAdapterFactory::class, 'eZ\Publish\Core\Pagination\Pagerfanta\AdapterFactory\SearchHitAdapterFactory');
