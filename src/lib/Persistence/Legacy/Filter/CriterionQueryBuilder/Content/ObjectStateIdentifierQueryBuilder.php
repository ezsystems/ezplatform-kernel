<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content;

use Doctrine\DBAL\Connection;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ObjectStateIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Core\Persistence\Legacy\Content\ObjectState\Gateway as ObjectStateGateway;

/**
 * @internal for internal use by Repository Filtering
 */
final class ObjectStateIdentifierQueryBuilder implements CriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof ObjectStateIdentifier;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentId $criterion */
        $queryBuilder
            ->joinOnce(
                'content',
                ObjectStateGateway::OBJECT_STATE_LINK_TABLE,
                'object_state_link',
                'content.id = object_state_link.contentobject_id',
            )
            ->joinOnce(
                'content',
                ObjectStateGateway::OBJECT_STATE_TABLE,
                'object_state',
                'object_state_link.contentobject_state_id = object_state.id'
            )
            ->joinOnce(
                'object_state',
                ObjectStateGateway::OBJECT_STATE_GROUP_TABLE,
                'object_state_group',
                'object_state.group_id = object_state_group.id'
            );

        $value = (array)$criterion->value;

        return $queryBuilder->expr()->in(
            'object_state.identifier',
            $queryBuilder->createNamedParameter($value, Connection::PARAM_STR_ARRAY)
        );
    }
}

class_alias(ObjectStateIdentifierQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\ObjectStateIdentifierQueryBuilder');
