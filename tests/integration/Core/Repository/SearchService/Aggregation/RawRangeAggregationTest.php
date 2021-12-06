<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Range;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawRangeAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResultEntry;

final class RawRangeAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        yield [
            new RawRangeAggregation(
                'raw_range',
                'content_version_no_i',
                [
                    new Range(null, 2),
                    new Range(2, 3),
                    new Range(3, null),
                ]
            ),
            new RangeAggregationResult(
                'raw_range',
                [
                    new RangeAggregationResultEntry(new Range(null, 2), 14),
                    new RangeAggregationResultEntry(new Range(2, 3), 3),
                    new RangeAggregationResultEntry(new Range(3, null), 1),
                ]
            ),
        ];
    }
}

class_alias(RawRangeAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\RawRangeAggregationTest');
