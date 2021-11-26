<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\User\Metadata;

use Doctrine\DBAL\Connection;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\UserMetadata;
use Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Core\Persistence\Legacy\Content\Location\Gateway as LocationGateway;

/**
 * @internal for internal use by Repository Filtering
 */
final class GroupQueryBuilder implements CriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof UserMetadata && $criterion->target === UserMetadata::GROUP;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\UserMetadata $criterion */
        $value = (array)$criterion->value;

        $queryBuilder
            ->joinOnce(
                'content',
                LocationGateway::CONTENT_TREE_TABLE,
                'user_location',
                'content.owner_id = user_location.contentobject_id'
            )
            ->joinOnce(
                'user_location',
                LocationGateway::CONTENT_TREE_TABLE,
                'user_group_location',
                'user_location.parent_node_id = user_group_location.node_id'
            );

        return $queryBuilder->expr()->in(
            'user_group_location.contentobject_id',
            $queryBuilder->createNamedParameter($value, Connection::PARAM_INT_ARRAY)
        );
    }
}

class_alias(GroupQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\User\Metadata\GroupQueryBuilder');
