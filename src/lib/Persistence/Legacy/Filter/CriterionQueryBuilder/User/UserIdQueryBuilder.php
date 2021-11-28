<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\User;

use Doctrine\DBAL\Connection;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\UserId;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;

/**
 * @internal for internal use by Repository Filtering
 */
final class UserIdQueryBuilder extends BaseUserCriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof UserId;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\UserId $criterion */
        parent::buildQueryConstraint($queryBuilder, $criterion);

        $value = (array)$criterion->value;

        return $queryBuilder->expr()->in(
            'user_storage.contentobject_id',
            $queryBuilder->createNamedParameter($value, Connection::PARAM_INT_ARRAY)
        );
    }
}

class_alias(UserIdQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\User\UserIdQueryBuilder');
