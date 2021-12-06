<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\Field;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Field\FloatStatsAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\StatsAggregationResult;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\AbstractAggregationTest;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\FixtureGenerator\FieldAggregationFixtureGenerator;

final class FloatStatsAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        yield [
            new FloatStatsAggregation('float_stats', 'content_type', 'float_field_2'),
            new StatsAggregationResult(
                'float_stats',
                5,
                1.0,
                7.75,
                3.8,
                19.0
            ),
        ];
    }

    protected function createFixturesForAggregation(Aggregation $aggregation): void
    {
        $generator = new FieldAggregationFixtureGenerator($this->getRepository());
        $generator->setContentTypeIdentifier('content_type');
        $generator->setFieldDefinitionIdentifier('float_field_2');
        $generator->setFieldTypeIdentifier('ezfloat');
        $generator->setValues([1.0, 2.5, 2.5, 5.25, 7.75]);

        $generator->execute();

        $this->refreshSearch($this->getRepository());
    }
}

class_alias(FloatStatsAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\Field\FloatStatsAggregationTest');
