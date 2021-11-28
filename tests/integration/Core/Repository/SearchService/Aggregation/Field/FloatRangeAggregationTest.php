<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\Field;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Field\FloatRangeAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Range;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\RangeAggregationResultEntry;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\AbstractAggregationTest;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\FixtureGenerator\FieldAggregationFixtureGenerator;

final class FloatRangeAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        yield [
            new FloatRangeAggregation('float_range', 'content_type', 'float_field', [
                new Range(null, 10.0),
                new Range(10.0, 25.0),
                new Range(25.0, 50.0),
                new Range(50.0, null),
            ]),
            new RangeAggregationResult(
                'float_range',
                [
                    new RangeAggregationResultEntry(new Range(null, 10.0), 4),
                    new RangeAggregationResultEntry(new Range(10.0, 25.0), 6),
                    new RangeAggregationResultEntry(new Range(25, 50), 10),
                    new RangeAggregationResultEntry(new Range(50, null), 20),
                ]
            ),
        ];
    }

    protected function createFixturesForAggregation(Aggregation $aggregation): void
    {
        $generator = new FieldAggregationFixtureGenerator($this->getRepository());
        $generator->setContentTypeIdentifier('content_type');
        $generator->setFieldDefinitionIdentifier('float_field');
        $generator->setFieldTypeIdentifier('ezfloat');
        $generator->setValues(range(1.0, 100.0, 2.5));

        $generator->execute();

        $this->refreshSearch($this->getRepository());
    }
}

class_alias(FloatRangeAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\Field\FloatRangeAggregationTest');
