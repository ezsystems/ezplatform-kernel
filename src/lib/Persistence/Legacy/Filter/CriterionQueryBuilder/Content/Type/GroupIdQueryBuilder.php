<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Type;

use Doctrine\DBAL\Connection;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeGroupId;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Core\Persistence\Legacy\Content\Type\Gateway as ContentTypeGateway;

/**
 * Content Type Group ID Criterion visitor query builder.
 *
 * @see \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeGroupId
 *
 * @internal for internal use by Repository Filtering
 */
final class GroupIdQueryBuilder extends BaseQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof ContentTypeGroupId;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeGroupId $criterion */
        $queryBuilder
            ->joinOnce(
                'content',
                ContentTypeGateway::CONTENT_TYPE_TO_GROUP_ASSIGNMENT_TABLE,
                'content_type_group_assignment',
                'content.contentclass_id = content_type_group_assignment.contentclass_id'
            );

        $queryBuilder
            ->joinOnce(
                'content_type_group_assignment',
                ContentTypeGateway::CONTENT_TYPE_GROUP_TABLE,
                'content_type_group',
                'content_type_group_assignment.group_id = content_type_group.id'
            );

        return $queryBuilder->expr()->in(
            'content_type_group.id',
            $queryBuilder->createNamedParameter(
                $criterion->value,
                Connection::PARAM_INT_ARRAY
            )
        );
    }
}

class_alias(GroupIdQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\Type\GroupIdQueryBuilder');
