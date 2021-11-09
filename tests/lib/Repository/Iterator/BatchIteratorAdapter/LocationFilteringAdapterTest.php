<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Iterator\BatchIteratorAdapter;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter\LocationFilteringAdapter;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationList;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\MatchAll;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use PHPUnit\Framework\TestCase;

final class LocationFilteringAdapterTest extends TestCase
{
    private const EXAMPLE_LANGUAGE_FILTER = ['eng-GB', 'pol-PL'];
    private const EXAMPLE_OFFSET = 10;
    private const EXAMPLE_LIMIT = 25;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testFetch(): void
    {
        $location1 = $this->createMock(Location::class);
        $location2 = $this->createMock(Location::class);
        $location3 = $this->createMock(Location::class);

        $locationList = new LocationList([
            'locations' => [
                $location1,
                $location2,
                $location3,
            ],
            'totalCount' => 3,
        ]);

        $expectedResults = [
            $location1,
            $location2,
            $location3,
        ];

        $originalFilter = new Filter();
        $originalFilter->withCriterion(new MatchAll());

        $expectedFilter = new Filter();
        $expectedFilter->withCriterion(new MatchAll());
        $expectedFilter->sliceBy(self::EXAMPLE_LIMIT, self::EXAMPLE_OFFSET);

        $locationService = $this->createMock(LocationService::class);
        $locationService
            ->expects($this->once())
            ->method('find')
            ->with($expectedFilter, self::EXAMPLE_LANGUAGE_FILTER)
            ->willReturn($locationList);

        $adapter = new LocationFilteringAdapter($locationService, $originalFilter, self::EXAMPLE_LANGUAGE_FILTER);

        self::assertSame(
            $expectedResults,
            iterator_to_array($adapter->fetch(self::EXAMPLE_OFFSET, self::EXAMPLE_LIMIT))
        );

        // Input $filter remains untouched
        self::assertSame(0, $originalFilter->getOffset());
        self::assertSame(0, $originalFilter->getLimit());
    }
}

class_alias(LocationFilteringAdapterTest::class, 'eZ\Publish\API\Repository\Tests\Iterator\BatchIteratorAdapter\LocationFilteringAdapterTest');
