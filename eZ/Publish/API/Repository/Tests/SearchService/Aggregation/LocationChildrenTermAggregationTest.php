<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Tests\SearchService\Aggregation;

use eZ\Publish\API\Repository\Tests\SearchService\Aggregation\DataSetBuilder\TermAggregationDataSetBuilder;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation\Location\LocationChildrenTermAggregation;
use eZ\Publish\API\Repository\Values\Content\Search\AggregationResult;

final class LocationChildrenTermAggregationTest extends AbstractAggregationTest
{
    /**
     * @dataProvider dataProviderForTestFindContentWithAggregation
     */
    public function testFindContentWithAggregation(
        Aggregation $aggregation,
        AggregationResult $expectedResult
    ): void {
        self::markTestSkipped('LocationChildrenTermAggregation is only available for Location search');
    }

    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        $aggregation = new LocationChildrenTermAggregation('children');

        $builder = new TermAggregationDataSetBuilder($aggregation);
        $builder->setExpectedEntries([
            1 => 5,
            5 => 5,
            43 => 3,
            13 => 1,
            2 => 1,
            44 => 1,
            48 => 1,
            58 => 1,
        ]);

        $builder->setEntryMapper([
            $this->getRepository()->getLocationService(),
            'loadLocation',
        ]);

        yield $builder->build();
    }
}
