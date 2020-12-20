<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Pagination\Tests;

use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\Core\Pagination\Pagerfanta\FixedSearchResultHitAdapter;
use PHPUnit\Framework\TestCase;

final class FixedSearchResultHitAdapterTest extends TestCase
{
    public function testFixedSearchResultHitAdapter(): void
    {
        $searchResult = $this->createExampleSearchResult();

        $adapter = new FixedSearchResultHitAdapter($searchResult);

        $this->assertEquals($searchResult->totalCount, $adapter->getNbResults());
        $this->assertEquals($searchResult->searchHits, $adapter->getSlice(0, 10));
        $this->assertEquals($searchResult->aggregations, $adapter->getAggregations());
        $this->assertEquals($searchResult->maxScore, $adapter->getMaxScore());
        $this->assertEquals($searchResult->time, $adapter->getTime());
        $this->assertEquals($searchResult->timedOut, $adapter->getTimedOut());
    }

    private function createExampleSearchResult(): SearchResult
    {
        $searchResult = new SearchResult();
        $searchResult->totalCount = 3;
        $searchResult->searchHits = [
            new SearchHit(),
            new SearchHit(),
            new SearchHit(),
        ];
        $searchResult->timedOut = true;
        $searchResult->time = 30;
        $searchResult->maxScore = 5.234;

        return $searchResult;
    }
}
