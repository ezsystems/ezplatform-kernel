<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Pagination\Tests;

use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation;
use eZ\Publish\API\Repository\Values\Content\Query\CriterionInterface;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Search\AggregationResultCollection;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchHitAdapter;
use PHPUnit\Framework\TestCase;

class LocationSearchHitAdapterTest extends TestCase
{
    private const EXAMPLE_LIMIT = 40;
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LANGUAGE_FILTER = [
        'languages' => ['eng-GB', 'pol-PL'],
        'useAlwaysAvailable' => true,
    ];

    private const EXAMPLE_RESULT_MAX_SCORE = 5.123;
    private const EXAMPLE_RESULT_TIME = 30.0;

    /** @var \eZ\Publish\API\Repository\SearchService|\PHPUnit\Framework\MockObject\MockObject */
    protected $searchService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->searchService = $this->createMock(SearchService::class);
    }

    /**
     * Returns the adapter to test.
     *
     * @param LocationQuery $query
     * @param SearchService $searchService
     * @param array $languageFilter
     *
     * @return LocationSearchHitAdapter
     */
    protected function getAdapter(LocationQuery $query, SearchService $searchService, array $languageFilter = [])
    {
        return new LocationSearchHitAdapter($query, $searchService, $languageFilter);
    }

    public function testGetNbResults()
    {
        $nbResults = 123;
        $query = $this->createTestQuery();

        // Count query will necessarily have a 0 limit.
        $countQuery = clone $query;
        $countQuery->aggregations = [];
        $countQuery->limit = 0;

        $searchResult = new SearchResult([
            'totalCount' => $nbResults,
        ]);

        $this->searchService
            ->expects($this->once())
            ->method('findLocations')
            ->with($countQuery, self::EXAMPLE_LANGUAGE_FILTER)
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query, $this->searchService, self::EXAMPLE_LANGUAGE_FILTER);

        $this->assertSame($nbResults, $adapter->getNbResults());
        // Running a 2nd time to ensure SearchService::findContent() is called only once.
        $this->assertSame($nbResults, $adapter->getNbResults());
    }

    public function testGetSlice()
    {
        $nbResults = 123;
        $aggregationsResults = new AggregationResultCollection();

        $query = $this->createTestQuery();

        // Injected query is being cloned to modify offset/limit,
        // so we need to do the same here for our assertions.
        $searchQuery = clone $query;
        $searchQuery->offset = self::EXAMPLE_OFFSET;
        $searchQuery->limit = self::EXAMPLE_LIMIT;
        $searchQuery->performCount = false;

        $hits = [];
        for ($i = 0; $i < self::EXAMPLE_LIMIT; ++$i) {
            $hits[] = new SearchHit([
                'valueObject' => $this->createMock(APILocation::class),
            ]);
        }

        $searchResult = new SearchResult([
            'searchHits' => $hits,
            'totalCount' => $nbResults,
            'aggregations' => $aggregationsResults,
            'maxScore' => self::EXAMPLE_RESULT_MAX_SCORE,
            'timedOut' => true,
            'time' => self::EXAMPLE_RESULT_TIME,
        ]);

        $this
            ->searchService
            ->expects($this->once())
            ->method('findLocations')
            ->with($searchQuery, self::EXAMPLE_LANGUAGE_FILTER)
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query, $this->searchService, self::EXAMPLE_LANGUAGE_FILTER);

        $this->assertSame(
            $this->getExpectedFinalResultFromHits($hits),
            $adapter->getSlice(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT)
        );

        $this->assertSame($nbResults, $adapter->getNbResults());
        $this->assertSame($aggregationsResults, $adapter->getAggregations());
        $this->assertSame(self::EXAMPLE_RESULT_MAX_SCORE, $adapter->getMaxScore());
        $this->assertTrue($adapter->getTimedOut());
        $this->assertSame(self::EXAMPLE_RESULT_TIME, $adapter->getTime());
    }

    public function testGetAggregations(): void
    {
        $exceptedAggregationsResults = new AggregationResultCollection();

        $query = $this->createTestQuery(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT);

        // Injected query is being cloned to modify offset/limit,
        // so we need to do the same here for our assertions.
        $aggregationQuery = clone $query;
        $aggregationQuery->offset = 0;
        $aggregationQuery->limit = 0;

        $searchResult = new SearchResult([
            'searchHits' => [],
            'totalCount' => 0,
            'aggregations' => $exceptedAggregationsResults,
        ]);

        $this
            ->searchService
            ->expects($this->once())
            ->method('findLocations')
            ->with($aggregationQuery, self::EXAMPLE_LANGUAGE_FILTER)
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query, $this->searchService, self::EXAMPLE_LANGUAGE_FILTER);

        $this->assertSame($exceptedAggregationsResults, $adapter->getAggregations());
        // Running a 2nd time to ensure SearchService::findContent() is called only once.
        $this->assertSame($exceptedAggregationsResults, $adapter->getAggregations());
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
        return $hits;
    }

    private function createTestQuery(int $limit = 25, int $offset = 0): LocationQuery
    {
        $query = new LocationQuery();
        $query->query = $this->createMock(CriterionInterface::class);
        $query->aggregations[] = $this->createMock(Aggregation::class);
        $query->sortClauses[] = $this->createMock(SortClause::class);
        $query->offset = $offset;
        $query->limit = $limit;

        return $query;
    }
}
