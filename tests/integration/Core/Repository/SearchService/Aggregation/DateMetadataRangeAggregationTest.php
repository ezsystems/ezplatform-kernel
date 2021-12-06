<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation;

use DateTime;
use DateTimeZone;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\DateMetadataRangeAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Range;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResultEntry;

final class DateMetadataRangeAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        $timezone = new DateTimeZone('+0000');

        yield '::MODIFIED' => [
            new DateMetadataRangeAggregation(
                'modification_date',
                DateMetadataRangeAggregation::MODIFIED,
                [
                    new Range(
                        null,
                        new DateTime('2003-01-01', $timezone)
                    ),
                    new Range(
                        new DateTime('2003-01-01', $timezone),
                        new DateTime('2004-01-01', $timezone)
                    ),
                    new Range(
                        new DateTime('2004-01-01', $timezone),
                        null
                    ),
                ]
            ),
            new RangeAggregationResult(
                'modification_date',
                [
                    new RangeAggregationResultEntry(
                        new Range(
                            null,
                            new DateTime('2003-01-01', $timezone)
                        ),
                        3
                    ),
                    new RangeAggregationResultEntry(
                        new Range(
                            new DateTime('2003-01-01', $timezone),
                            new DateTime('2004-01-01', $timezone)
                        ),
                        3
                    ),
                    new RangeAggregationResultEntry(
                        new Range(
                            new DateTime('2004-01-01', $timezone),
                            null
                        ),
                        12
                    ),
                ]
            ),
        ];

        yield '::PUBLISHED' => [
            new DateMetadataRangeAggregation(
                'publication_date',
                DateMetadataRangeAggregation::PUBLISHED,
                [
                    new Range(
                        null,
                        new DateTime('2003-01-01', $timezone)
                    ),
                    new Range(
                        new DateTime('2003-01-01', $timezone),
                        new DateTime('2004-01-01', $timezone)
                    ),
                    new Range(
                        new DateTime('2004-01-01', $timezone),
                        null
                    ),
                ]
            ),
            new RangeAggregationResult(
                'publication_date',
                [
                    new RangeAggregationResultEntry(
                        new Range(
                            null,
                            new DateTime('2003-01-01', $timezone)
                        ),
                        6
                    ),
                    new RangeAggregationResultEntry(
                        new Range(
                            new DateTime('2003-01-01', $timezone),
                            new DateTime('2004-01-01', $timezone)
                        ),
                        2
                    ),
                    new RangeAggregationResultEntry(
                        new Range(
                            new DateTime('2004-01-01', $timezone),
                            null
                        ),
                        10
                    ),
                ]
            ),
        ];
    }
}

class_alias(DateMetadataRangeAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\DateMetadataRangeAggregationTest');
