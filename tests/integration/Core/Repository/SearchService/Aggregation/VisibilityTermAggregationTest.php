<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\VisibilityTermAggregation;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\DataSetBuilder\TermAggregationDataSetBuilder;

final class VisibilityTermAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        $aggregation = new VisibilityTermAggregation('visibility');

        $builder = new TermAggregationDataSetBuilder($aggregation);
        $builder->setExpectedEntries([
            true => 18,
        ]);

        yield $builder->build();
    }
}

class_alias(VisibilityTermAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\VisibilityTermAggregationTest');
