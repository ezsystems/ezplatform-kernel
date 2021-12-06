<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Pagination;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Core\Pagination\Pagerfanta\LocationSearchAdapter;

class LocationSearchAdapterTest extends LocationSearchHitAdapterTest
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery $query
     * @param \Ibexa\Contracts\Core\Repository\SearchService $searchService
     *
     * @return \Ibexa\Core\Pagination\Pagerfanta\LocationSearchAdapter
     */
    protected function getAdapter(LocationQuery $query, SearchService $searchService, array $languageFilter = [])
    {
        return new LocationSearchAdapter($query, $searchService, $languageFilter);
    }

    /**
     * Returns expected result from adapter from search hits.
     *
     * @param $hits
     *
     * @return mixed
     */
    protected function getExpectedFinalResultFromHits($hits)
    {
        $expectedResult = [];

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit[] $hits */
        foreach ($hits as $hit) {
            $expectedResult[] = $hit->valueObject;
        }

        return $expectedResult;
    }
}

class_alias(LocationSearchAdapterTest::class, 'eZ\Publish\Core\Pagination\Tests\LocationSearchAdapterTest');
