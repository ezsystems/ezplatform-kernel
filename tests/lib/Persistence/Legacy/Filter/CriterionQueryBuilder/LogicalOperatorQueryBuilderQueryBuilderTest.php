<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Legacy\Filter\CriterionQueryBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\ContentIdQueryBuilder;
use Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\LanguageCodeQueryBuilder;
use Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location\ParentLocationIdQueryBuilder;
use Ibexa\Tests\Core\Persistence\Legacy\Filter\BaseCriterionVisitorQueryBuilderTestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\LogicalAndQueryBuilder
 * @covers \Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\LogicalOrQueryBuilder
 * @covers \Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\LogicalNotQueryBuilder
 */
final class LogicalOperatorQueryBuilderQueryBuilderTest extends BaseCriterionVisitorQueryBuilderTestCase
{
    public function getFilteringCriteriaQueryData(): iterable
    {
        yield 'Parent Location ID=1 AND Language Code=eng-GB' => [
            new Criterion\LogicalAnd(
                [
                    new Criterion\ParentLocationId(1),
                    new Criterion\LanguageCode('eng-GB'),
                ]
            ),
            '(location.parent_node_id IN (:dcValue1)) AND ((language.locale IN (:dcValue2)) OR (version.language_mask & 1 = 1))',
            ['dcValue1' => [1], 'dcValue2' => ['eng-GB']],
        ];

        yield 'Language Code=eng-US OR Parent Location ID=2' => [
            new Criterion\LogicalOr(
                [
                    new Criterion\LanguageCode('eng-GB'),
                    new Criterion\ParentLocationId(2),
                ]
            ),
            '((language.locale IN (:dcValue1)) OR (version.language_mask & 1 = 1)) OR (location.parent_node_id IN (:dcValue2))',
            ['dcValue1' => ['eng-GB'], 'dcValue2' => [2]],
        ];

        yield 'NOT(Content ID=1 OR (Parent Location ID=2 AND Content ID = 1)' => [
            new Criterion\LogicalNot(
                new Criterion\LogicalOr(
                    [
                        new Criterion\ContentId(1),
                        new Criterion\LogicalAnd(
                            [
                                new Criterion\ParentLocationId(2),
                                new Criterion\ContentId(1),
                            ]
                        ),
                    ]
                )
            ),
            'NOT ((content.id IN (:dcValue1)) OR ((location.parent_node_id IN (:dcValue2)) AND (content.id IN (:dcValue3))))',
            ['dcValue1' => [1], 'dcValue2' => [2], 'dcValue3' => [1]],
        ];
    }

    protected function getCriterionQueryBuilders(): iterable
    {
        return [
            new ParentLocationIdQueryBuilder(),
            new LanguageCodeQueryBuilder(),
            new ContentIdQueryBuilder(),
        ];
    }
}

class_alias(LogicalOperatorQueryBuilderQueryBuilderTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Filter\CriterionQueryBuilder\LogicalOperatorQueryBuilderQueryBuilderTest');
