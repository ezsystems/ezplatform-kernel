<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Search\Common\FieldValueMapper;

use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\Core\Search\Common\FieldValueMapper\Aggregate;
use eZ\Publish\Core\Search\Common\FieldValueMapper\BooleanMapper;
use eZ\Publish\SPI\Search\Field;
use eZ\Publish\SPI\Search\FieldType\BooleanField;
use eZ\Publish\SPI\Search\FieldType\FloatField;
use PHPUnit\Framework\TestCase;

/**
 * @covers \eZ\Publish\Core\Search\Common\FieldValueMapper\Aggregate
 */
final class AggregateTest extends TestCase
{
    private const MAPPED_VALUE = true;

    /** @var \eZ\Publish\Core\Search\Common\FieldValueMapper\Aggregate */
    private $aggregateMapper;

    public function setUp(): void
    {
        $this->aggregateMapper = new Aggregate();
    }

    public function testMapUsingSimpleMapper(): void
    {
        $booleanMapperMock = $this->createMock(BooleanMapper::class);
        $this->aggregateMapper->addMapper($booleanMapperMock, BooleanField::class);

        $booleanField = new BooleanField();
        $searchFieldMock = $this->createMock(Field::class);
        $searchFieldMock
            ->method('getType')
            ->willReturn($booleanField);
        $booleanMapperMock
            ->method('map')
            ->with($searchFieldMock)
            ->willReturn(self::MAPPED_VALUE);

        self::assertSame(self::MAPPED_VALUE, $this->aggregateMapper->map($searchFieldMock));
    }

    public function testMapUsingCanMap(): void
    {
        $booleanMapper = new BooleanMapper();
        $this->aggregateMapper->addMapper($booleanMapper);

        $booleanField = new BooleanField();
        $searchFieldMock = $this->createMock(Field::class);
        $searchFieldMock
            ->method('getType')
            ->willReturn($booleanField);
        $searchFieldMock
            ->method('getValue')
            ->willReturn(self::MAPPED_VALUE);

        self::assertSame(self::MAPPED_VALUE, $this->aggregateMapper->map($searchFieldMock));
    }

    public function testMapThrowsNotImplementedException(): void
    {
        $booleanMapperMock = $this->createMock(BooleanMapper::class);
        $this->aggregateMapper->addMapper($booleanMapperMock);

        $floatFieldMock = $this->createMock(FloatField::class);
        $searchFieldMock = $this->createMock(Field::class);
        $searchFieldMock
            ->method('getType')
            ->willReturn($floatFieldMock);
        $booleanMapperMock
            ->method('canMap')
            ->willReturn(false);

        $this->expectException(NotImplementedException::class);
        $this->expectExceptionMessage('No mapper available for: ' . get_class($floatFieldMock));
        $this->aggregateMapper->map($searchFieldMock);
    }
}
