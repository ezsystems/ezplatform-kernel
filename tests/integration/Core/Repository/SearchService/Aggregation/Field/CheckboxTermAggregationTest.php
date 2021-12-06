<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\Field;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Field\CheckboxTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry;
use Ibexa\Core\FieldType\Checkbox\Value as CheckboxValue;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\AbstractAggregationTest;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\FixtureGenerator\FieldAggregationFixtureGenerator;

final class CheckboxTermAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        yield [
            new CheckboxTermAggregation('checkbox_term', 'content_type', 'boolean'),
            new TermAggregationResult(
                'checkbox_term',
                [
                    new TermAggregationResultEntry(true, 3),
                    new TermAggregationResultEntry(false, 2),
                ]
            ),
        ];
    }

    protected function createFixturesForAggregation(Aggregation $aggregation): void
    {
        $generator = new FieldAggregationFixtureGenerator($this->getRepository());
        $generator->setContentTypeIdentifier('content_type');
        $generator->setFieldDefinitionIdentifier('boolean');
        $generator->setFieldTypeIdentifier('ezboolean');
        $generator->setValues([
            new CheckboxValue(true),
            new CheckboxValue(true),
            new CheckboxValue(true),
            new CheckboxValue(false),
            new CheckboxValue(false),
        ]);

        $generator->execute();

        $this->refreshSearch($this->getRepository());
    }
}

class_alias(CheckboxTermAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\Field\CheckboxTermAggregationTest');
