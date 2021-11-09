<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Values\Content\Query\Aggregation\Location;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Location\SubtreeTermAggregation;
use PHPUnit\Framework\TestCase;

final class SubtreeTermAggregationTest extends TestCase
{
    private const EXAMPLE_PATH_STRING = '/1/2/';
    private const EXAMPLE_AGGREGATION_NAME = 'foo';

    public function testConstruct(): void
    {
        $aggregation = new SubtreeTermAggregation(
            self::EXAMPLE_AGGREGATION_NAME,
            self::EXAMPLE_PATH_STRING
        );

        $this->assertEquals(self::EXAMPLE_AGGREGATION_NAME, $aggregation->getName());
        $this->assertEquals(self::EXAMPLE_PATH_STRING, $aggregation->getPathString());
    }

    public function testConstructThrowsInvalidArgumentExceptionOnInvalidPathString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage("'/INVALID/PATH' value must follow the path string format, e.g. /1/2/");

        $aggregation = new SubtreeTermAggregation('foo', '/INVALID/PATH');
    }

    public function testFromLocation(): void
    {
        $location = $this->createMock(Location::class);
        $location->method('__get')->with('pathString')->willReturn(self::EXAMPLE_PATH_STRING);

        $aggregation = SubtreeTermAggregation::fromLocation(self::EXAMPLE_AGGREGATION_NAME, $location);

        $this->assertEquals(self::EXAMPLE_AGGREGATION_NAME, $aggregation->getName());
        $this->assertEquals(self::EXAMPLE_PATH_STRING, $aggregation->getPathString());
    }
}

class_alias(SubtreeTermAggregationTest::class, 'eZ\Publish\API\Repository\Tests\Values\Content\Query\Aggregation\Location\SubtreeTermAggregationTest');
