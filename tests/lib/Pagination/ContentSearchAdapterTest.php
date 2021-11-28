<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Pagination;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Core\Pagination\Pagerfanta\ContentSearchAdapter;

class ContentSearchAdapterTest extends ContentSearchHitAdapterTest
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query $query
     * @param \Ibexa\Contracts\Core\Repository\SearchService $searchService
     * @param array $languageFilter
     *
     * @return \Ibexa\Core\Pagination\Pagerfanta\ContentSearchAdapter
     */
    protected function getAdapter(Query $query, SearchService $searchService, array $languageFilter = [])
    {
        return new ContentSearchAdapter($query, $searchService, $languageFilter);
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

class_alias(ContentSearchAdapterTest::class, 'eZ\Publish\Core\Pagination\Tests\ContentSearchAdapterTest');
