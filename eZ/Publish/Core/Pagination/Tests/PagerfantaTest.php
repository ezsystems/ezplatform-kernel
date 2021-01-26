<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Pagination\Tests;

use eZ\Publish\API\Repository\Values\Content\Search\AggregationResultCollection;
use eZ\Publish\Core\Pagination\Pagerfanta\Pagerfanta;
use eZ\Publish\Core\Pagination\Pagerfanta\SearchResultAdapter;
use PHPUnit\Framework\TestCase;

final class PagerfantaTest extends TestCase
{
    private const EXAMPLE_TIME_RESULT = 30.0;
    private const EXAMPLE_MAX_SCORE_RESULT = 5.12354;

    /** @var \eZ\Publish\Core\Pagination\Pagerfanta\SearchResultAdapter|\PHPUnit\Framework\MockObject\MockObject */
    private $adapter;

    /** @var \eZ\Publish\Core\Pagination\Pagerfanta\Pagerfanta */
    private $pagerfanta;

    protected function setUp(): void
    {
        $this->adapter = $this->createMock(SearchResultAdapter::class);
        $this->pagerfanta = new Pagerfanta($this->adapter);
    }

    public function testGetAggregations(): void
    {
        $aggregations = new AggregationResultCollection();

        $this->adapter->method('getAggregations')->willReturn($aggregations);

        $this->assertEquals(
            $aggregations,
            $this->pagerfanta->getAggregations()
        );
    }

    public function testGetTime(): void
    {
        $this->adapter->method('getTime')->willReturn(self::EXAMPLE_TIME_RESULT);

        $this->assertEquals(
            self::EXAMPLE_TIME_RESULT,
            $this->pagerfanta->getTime()
        );
    }

    public function testGetTimedOut(): void
    {
        $this->adapter->method('getTimedOut')->willReturn(true);

        $this->assertTrue(
            $this->pagerfanta->getTimedOut()
        );
    }

    public function testGetMaxScore(): void
    {
        $this->adapter->method('getMaxScore')->willReturn(self::EXAMPLE_MAX_SCORE_RESULT);

        $this->assertEquals(
            self::EXAMPLE_MAX_SCORE_RESULT,
            $this->pagerfanta->getMaxScore()
        );
    }
}
