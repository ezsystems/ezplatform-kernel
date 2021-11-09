<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Type;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeGroupId;
use Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Type\GroupIdQueryBuilder;
use Ibexa\Tests\Core\Persistence\Legacy\Filter\BaseCriterionVisitorQueryBuilderTestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Type\GroupIdQueryBuilder
 */
class ContentTypeGroupIdQueryBuilderTest extends BaseCriterionVisitorQueryBuilderTestCase
{
    public function getFilteringCriteriaQueryData(): iterable
    {
        yield 'Content Type Group ID=1' => [
            new ContentTypeGroupId(1),
            'content_type_group.id IN (:dcValue1)',
            ['dcValue1' => [1]],
        ];

        yield 'Content Type Group ID IN (1, 2)' => [
            new ContentTypeGroupId([1, 2]),
            'content_type_group.id IN (:dcValue1)',
            ['dcValue1' => [1, 2]],
        ];
    }

    protected function getCriterionQueryBuilders(): iterable
    {
        return [new GroupIdQueryBuilder()];
    }
}
