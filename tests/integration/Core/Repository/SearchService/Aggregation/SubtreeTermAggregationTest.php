<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Location\SubtreeTermAggregation;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\DataSetBuilder\TermAggregationDataSetBuilder;

final class SubtreeTermAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        $aggregation = new SubtreeTermAggregation('subtree', '/1/5/');

        $builder = new TermAggregationDataSetBuilder($aggregation);
        $builder->setExpectedEntries([
            5 => 7,
            13 => 1,
            44 => 1,
        ]);

        $builder->setEntryMapper([
            $this->getRepository()->getLocationService(),
            'loadLocation',
        ]);

        yield $builder->build();
    }
}

class_alias(SubtreeTermAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\SubtreeTermAggregationTest');
