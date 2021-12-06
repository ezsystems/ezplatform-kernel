<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\Field;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Field\TimeRangeAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Range;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResultEntry;
use Ibexa\Core\FieldType\Time\Value as TimeValue;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\AbstractAggregationTest;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\FixtureGenerator\FieldAggregationFixtureGenerator;

final class TimeRangeAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        yield [
            new TimeRangeAggregation(
                'time_term',
                'content_type',
                'time_field',
                [
                    new Range(null, mktime(7, 0, 0, 0, 0, 0)),
                    new Range(
                        mktime(7, 0, 0, 0, 0, 0),
                        mktime(12, 0, 0, 0, 0, 0)
                    ),
                    new Range(mktime(12, 0, 0, 0, 0, 0), null),
                ]
            ),
            new RangeAggregationResult(
                'time_term',
                [
                    new RangeAggregationResultEntry(
                        new Range(null, mktime(7, 0, 0, 0, 0, 0)),
                        2
                    ),
                    new RangeAggregationResultEntry(
                        new Range(
                            mktime(7, 0, 0, 0, 0, 0),
                            mktime(12, 0, 0, 0, 0, 0)
                        ),
                        2
                    ),
                    new RangeAggregationResultEntry(
                        new Range(mktime(12, 0, 0, 0, 0, 0), null),
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
        $generator->setFieldDefinitionIdentifier('time_field');
        $generator->setFieldTypeIdentifier('eztime');
        $generator->setValues([
            new TimeValue(mktime(6, 45, 0, 0, 0, 0)),
            new TimeValue(mktime(7, 0, 0, 0, 0, 0)),
            new TimeValue(mktime(6, 30, 0, 0, 0, 0)),
            new TimeValue(mktime(11, 45, 0, 0, 0, 0)),
            new TimeValue(mktime(16, 00, 0, 0, 0, 0)),
            new TimeValue(mktime(17, 00, 0, 0, 0, 0)),
            new TimeValue(mktime(17, 30, 0, 0, 0, 0)),
        ]);

        $generator->execute();

        $this->refreshSearch($this->getRepository());
    }
}

class_alias(TimeRangeAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\Field\TimeRangeAggregationTest');
