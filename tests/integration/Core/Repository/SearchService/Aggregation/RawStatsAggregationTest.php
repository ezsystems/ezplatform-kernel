<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawStatsAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\StatsAggregationResult;

final class RawStatsAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        yield [
            new RawStatsAggregation(
                'raw_stats',
                'content_version_no_i'
            ),
            new StatsAggregationResult(
                'raw_stats',
                18,
                1.0,
                4.0,
                1.3333333333333333,
                24.0
            ),
        ];
    }
}

class_alias(RawStatsAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\RawStatsAggregationTest');
