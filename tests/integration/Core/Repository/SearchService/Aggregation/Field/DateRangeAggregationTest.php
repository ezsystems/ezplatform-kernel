<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\Field;

use DateTime;
use DateTimeZone;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Field\DateRangeAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Range;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResultEntry;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\AbstractAggregationTest;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\FixtureGenerator\FieldAggregationFixtureGenerator;

final class DateRangeAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        $timezone = new DateTimeZone('+0000');

        yield [
            new DateRangeAggregation(
                'date_range',
                'content_type',
                'date_field',
                [
                    new Range(
                        null,
                        new DateTime('2020-07-01T00:00:00', $timezone)
                    ),
                    new Range(
                        new DateTime('2020-07-01T00:00:00', $timezone),
                        new DateTime('2020-08-01T00:00:00', $timezone)
                    ),
                    new Range(
                        new DateTime('2020-08-01T00:00:00', $timezone),
                        null
                    ),
                ]
            ),
            new RangeAggregationResult(
                'date_range',
                [
                    new RangeAggregationResultEntry(
                        new Range(
                            null,
                            new DateTime('2020-07-01 00:00:00', $timezone)
                        ),
                        3,
                    ),
                    new RangeAggregationResultEntry(
                        new Range(
                            new DateTime('2020-07-01T00:00:00', $timezone),
                            new DateTime('2020-08-01T00:00:00', $timezone)
                        ),
                        3
                    ),
                    new RangeAggregationResultEntry(
                        new Range(
                            new DateTime('2020-08-01T00:00:00', $timezone),
                            null
                        ),
                        3
                    ),
                ]
            ),
        ];
    }

    protected function createFixturesForAggregation(Aggregation $aggregation): void
    {
        $generator = new FieldAggregationFixtureGenerator($this->getRepository());
        $generator->setContentTypeIdentifier('content_type');
        $generator->setFieldDefinitionIdentifier('date_field');
        $generator->setFieldTypeIdentifier('ezdate');
        $generator->setValues([
            new DateTime('2020-05-01 00:00:00'),
            new DateTime('2020-06-30 00:00:00'),
            new DateTime('2020-06-30 12:00:00'),
            new DateTime('2020-07-01 00:00:00'),
            new DateTime('2020-07-01 12:00:00'),
            new DateTime('2020-07-30 12:00:00'),
            new DateTime('2020-08-01 00:00:01'),
            new DateTime('2020-08-01 00:00:02'),
            new DateTime('2020-08-01 00:00:03'),
        ]);

        $generator->execute();

        $this->refreshSearch($this->getRepository());
    }
}

class_alias(DateRangeAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\Field\DateRangeAggregationTest');
