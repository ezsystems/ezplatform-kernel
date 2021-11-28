<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\ObjectStateTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\DataSetBuilder\TermAggregationDataSetBuilder;

final class ObjectStateTermAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        $aggregation = new ObjectStateTermAggregation('object_state', 'ez_lock');

        $builder = new TermAggregationDataSetBuilder($aggregation);
        $builder->setExpectedEntries([
            // TODO: Change the state of some content objects to have better test data
            'not_locked' => 18,
        ]);

        $builder->setEntryMapper(
            function (string $identifier): ObjectState {
                $objectStateService = $this->getRepository()->getObjectStateService();

                static $objectStateGroup = null;
                if ($objectStateGroup === null) {
                    $objectStateGroup = $objectStateService->loadObjectStateGroupByIdentifier('ez_lock');
                }

                return $objectStateService->loadObjectStateByIdentifier($objectStateGroup, $identifier);
            }
        );

        yield $builder->build();
    }
}

class_alias(ObjectStateTermAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\ObjectStateTermAggregationTest');
