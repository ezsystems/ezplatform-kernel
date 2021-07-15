<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace SearchService\Aggregation\Field;

use eZ\Publish\API\Repository\Tests\SearchService\Aggregation\AbstractAggregationTest;
use eZ\Publish\API\Repository\Tests\SearchService\Aggregation\DataSetBuilder\TermAggregationDataSetBuilder;
use eZ\Publish\API\Repository\Tests\SearchService\Aggregation\FixtureGenerator\FieldAggregationFixtureGenerator;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation;
use eZ\Publish\API\Repository\Values\Content\Query\Aggregation\Field\RelationTermAggregation;

final class RelationListTermAggregationTest extends AbstractAggregationTest
{
    private const CONTENT_A = 4;
    private const CONTENT_B = 10;
    private const CONTENT_C = 58;

    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        $aggregation = new RelationTermAggregation(
            'relation_list_term',
            'content_type',
            'relation_list_field'
        );

        $builder = new TermAggregationDataSetBuilder($aggregation);
        $builder->setExpectedEntries([
            self::CONTENT_A => 4,
            self::CONTENT_C => 3,
            self::CONTENT_B => 2,
        ]);

        $builder->setEntryMapper([
            $this->getRepository()->getContentService(),
            'loadContentInfo',
        ]);

        yield $builder->build();
    }

    protected function createFixturesForAggregation(Aggregation $aggregation): void
    {
        $this->getRepository()->getPermissionResolver()->setCurrentUserReference(
            $this->getRepository()->getUserService()->loadUserByLogin('admin')
        );

        $generator = new FieldAggregationFixtureGenerator($this->getRepository());
        $generator->setContentTypeIdentifier('content_type');
        $generator->setFieldDefinitionIdentifier('relation_list_field');
        $generator->setFieldTypeIdentifier('ezobjectrelationlist');
        $generator->setValues([
            [self::CONTENT_A, self::CONTENT_B],
            [self::CONTENT_A, self::CONTENT_B, self::CONTENT_C],
            [self::CONTENT_A, self::CONTENT_C],
            [self::CONTENT_A],
            [self::CONTENT_C],
        ]);
        $generator->execute();

        $this->refreshSearch($this->getRepository());
    }
}
