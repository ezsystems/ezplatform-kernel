<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Iterator\BatchIteratorAdapter;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter\AbstractSearchAdapter;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\MatchAll;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Iterator;
use PHPUnit\Framework\TestCase;

abstract class AbstractSearchAdapterTest extends TestCase
{
    protected const EXAMPLE_LANGUAGE_FILTER = [
        'languages' => ['eng-GB', 'pol-PL'],
        'useAlwaysAvailable' => true,
    ];

    protected const EXAMPLE_OFFSET = 7;
    protected const EXAMPLE_LIMIT = 13;

    final public function testFetch(): void
    {
        $expectedIterator = $this->createMock(Iterator::class);

        $searchResults = $this->createMock(SearchResult::class);
        $searchResults->method('getIterator')->willReturn($expectedIterator);

        $originalQuery = $this->newQuery();
        $originalQuery->filter = new MatchAll();

        $expectedQuery = $this->newQuery();
        $expectedQuery->filter = new MatchAll();
        $expectedQuery->offset = self::EXAMPLE_OFFSET;
        $expectedQuery->limit = self::EXAMPLE_LIMIT;

        $searchService = $this->createMock(SearchService::class);
        $searchService
            ->expects($this->once())
            ->method($this->getExpectedFindMethod())
            ->with($expectedQuery, self::EXAMPLE_LANGUAGE_FILTER, true)
            ->willReturn($searchResults);

        $adapter = $this->createAdapterUnderTest($searchService, $originalQuery, self::EXAMPLE_LANGUAGE_FILTER, true);

        $this->assertSame(
            $expectedIterator,
            $adapter->fetch(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT),
        );

        $this->assertEquals(0, $originalQuery->offset);
        $this->assertEquals(25, $originalQuery->limit);
    }

    abstract protected function createAdapterUnderTest(
        SearchService $searchService,
        Query $query,
        array $languageFilter,
        bool $filterOnPermissions
    ): AbstractSearchAdapter;

    abstract protected function getExpectedFindMethod(): string;

    protected function newQuery(): Query
    {
        return new Query();
    }
}

class_alias(AbstractSearchAdapterTest::class, 'eZ\Publish\API\Repository\Tests\Iterator\BatchIteratorAdapter\AbstractSearchAdapterTest');
