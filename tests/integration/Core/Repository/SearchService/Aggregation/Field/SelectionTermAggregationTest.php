<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\Field;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Field\SelectionTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\AbstractAggregationTest;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\FixtureGenerator\FieldAggregationFixtureGenerator;

final class SelectionTermAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        yield [
            new SelectionTermAggregation(
                'selection_term',
                'content_type',
                'selection_field'
            ),
            new TermAggregationResult(
                'selection_term',
                [
                    new TermAggregationResultEntry('foo', 3),
                    new TermAggregationResultEntry('bar', 2),
                    new TermAggregationResultEntry('baz', 1),
                ]
            ),
        ];
    }

    protected function createFixturesForAggregation(Aggregation $aggregation): void
    {
        $generator = new FieldAggregationFixtureGenerator($this->getRepository());
        $generator->setContentTypeIdentifier('content_type');
        $generator->setFieldDefinitionIdentifier('selection_field');
        $generator->setFieldTypeIdentifier('ezselection');
        $generator->setValues([
            [0],
            [0, 1],
            [0, 1, 2],
        ]);

        $generator->setFieldDefinitionCreateStructConfigurator(
            static function (FieldDefinitionCreateStruct $createStruct): void {
                $createStruct->fieldSettings = [
                    'isMultiple' => true,
                    'options' => [
                        0 => 'foo',
                        1 => 'bar',
                        2 => 'baz',
                    ],
                ];
            },
        );

        $generator->execute();

        $this->refreshSearch($this->getRepository());
    }
}

class_alias(SelectionTermAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\Field\SelectionTermAggregationTest');
