<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content;

use Doctrine\DBAL\Connection;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ObjectStateId;
use Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Core\Persistence\Legacy\Content\ObjectState\Gateway;

/**
 * @internal for internal use by Repository Filtering
 */
final class ObjectStateIdQueryBuilder implements CriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof ObjectStateId;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ObjectStateId $criterion */
        $queryBuilder
            ->joinOnce(
                'content',
                Gateway::OBJECT_STATE_LINK_TABLE,
                'object_state_link',
                'content.id = object_state_link.contentobject_id',
            );

        $value = (array)$criterion->value;

        return $queryBuilder->expr()->in(
            'object_state_link.contentobject_state_id',
            $queryBuilder->createNamedParameter($value, Connection::PARAM_INT_ARRAY)
        );
    }
}

class_alias(ObjectStateIdQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\ObjectStateIdQueryBuilder');
