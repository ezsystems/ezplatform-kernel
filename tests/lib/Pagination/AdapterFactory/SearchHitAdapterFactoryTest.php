<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Pagination\AdapterFactory;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Core\Pagination\Pagerfanta\AdapterFactory\SearchHitAdapterFactory;
use Ibexa\Core\Pagination\Pagerfanta\ContentSearchHitAdapter;
use Ibexa\Core\Pagination\Pagerfanta\FixedSearchResultHitAdapter;
use Ibexa\Core\Pagination\Pagerfanta\LocationSearchHitAdapter;
use PHPUnit\Framework\TestCase;

final class SearchHitAdapterFactoryTest extends TestCase
{
    private const EXAMPLE_LANGUAGE_FILTER = [
        'language' => 'eng-GB',
    ];

    /** @var \Ibexa\Contracts\Core\Repository\SearchService|\PHPUnit\Framework\MockObject\MockObject */
    private $searchService;

    /** @var \Ibexa\Core\Pagination\Pagerfanta\AdapterFactory\SearchHitAdapterFactory */
    private $searchHitAdapterFactory;

    protected function setUp(): void
    {
        $this->searchService = $this->createMock(SearchService::class);
        $this->searchHitAdapterFactory = new SearchHitAdapterFactory($this->searchService);
    }

    public function testCreateAdapterForContentQuery(): void
    {
        $query = new Query();

        $this->assertEquals(
            new ContentSearchHitAdapter(
                $query,
                $this->searchService,
                self::EXAMPLE_LANGUAGE_FILTER
            ),
            $this->searchHitAdapterFactory->createAdapter($query, self::EXAMPLE_LANGUAGE_FILTER)
        );
    }

    public function testCreateAdapterForLocationQuery(): void
    {
        $query = new LocationQuery();

        $this->assertEquals(
            new LocationSearchHitAdapter(
                $query,
                $this->searchService,
                self::EXAMPLE_LANGUAGE_FILTER
            ),
            $this->searchHitAdapterFactory->createAdapter($query, self::EXAMPLE_LANGUAGE_FILTER)
        );
    }

    /**
     * @dataProvider dataProviderForCreateFixedAdapter
     */
    public function testCreateFixedAdapter(Query $query, string $expectedSearchMethod): void
    {
        $hits = [
            new SearchHit(),
            new SearchHit(),
            new SearchHit(),
        ];

        $searchResult = new SearchResult([
            'searchHits' => $hits,
            'totalCount' => count($hits),
        ]);

        $this->searchService
            ->expects($this->once())
            ->method($expectedSearchMethod)
            ->with($query, self::EXAMPLE_LANGUAGE_FILTER)
            ->willReturn($searchResult);

        $this->assertEquals(
            new FixedSearchResultHitAdapter($searchResult),
            $this->searchHitAdapterFactory->createFixedAdapter($query, self::EXAMPLE_LANGUAGE_FILTER)
        );
    }

    public function dataProviderForCreateFixedAdapter(): iterable
    {
        yield 'content query' => [
            new Query(),
            'findContent',
        ];

        yield 'location query' => [
            new LocationQuery(),
            'findLocations',
        ];
    }
}

class_alias(SearchHitAdapterFactoryTest::class, 'eZ\Publish\Core\Pagination\Tests\AdapterFactory\SearchHitAdapterFactoryTest');
