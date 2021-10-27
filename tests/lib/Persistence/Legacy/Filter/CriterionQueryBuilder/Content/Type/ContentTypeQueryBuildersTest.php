<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Type;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Type\IdentifierQueryBuilder;
use Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Type\IdQueryBuilder;
use Ibexa\Tests\Core\Persistence\Legacy\Filter\BaseCriterionVisitorQueryBuilderTestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Type\IdentifierQueryBuilder
 * @covers \Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Type\IdQueryBuilder
 */
final class ContentTypeQueryBuildersTest extends BaseCriterionVisitorQueryBuilderTestCase
{
    public function getFilteringCriteriaQueryData(): iterable
    {
        yield 'Content Type Identifier=article' => [
            new Criterion\ContentTypeIdentifier('article'),
            'content_type.identifier IN (:dcValue1)',
            ['dcValue1' => ['article']],
        ];

        yield 'Content Type ID=1' => [
            new Criterion\ContentTypeId(3),
            'content_type.id IN (:dcValue1)',
            ['dcValue1' => [3]],
        ];

        yield 'Content Type Identifier=folder OR Content Type ID IN (1, 2)' => [
            new Criterion\LogicalOr(
                [
                    new Criterion\ContentTypeIdentifier('folder'),
                    new Criterion\ContentTypeId([1, 2]),
                ]
            ),
            '(content_type.identifier IN (:dcValue1)) OR (content_type.id IN (:dcValue2))',
            ['dcValue1' => ['folder'], 'dcValue2' => [1, 2]],
        ];
    }

    protected function getCriterionQueryBuilders(): iterable
    {
        return [
            new IdentifierQueryBuilder(),
            new IdQueryBuilder(),
        ];
    }
}
