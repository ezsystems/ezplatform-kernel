<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\User;

use Doctrine\DBAL\Connection;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\UserLogin;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;

/**
 * @internal for internal use by Repository Filtering
 */
final class UserLoginQueryBuilder extends BaseUserCriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof UserLogin;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\UserId $criterion */
        parent::buildQueryConstraint($queryBuilder, $criterion);

        $expr = $queryBuilder->expr();
        if (Operator::LIKE === $criterion->operator) {
            return $expr->like(
                'user_storage.login',
                $queryBuilder->createNamedParameter(
                    $this->transformCriterionValueForLikeExpression($criterion->value)
                )
            );
        }

        $value = (array)$criterion->value;

        return $expr->in(
            'user_storage.login',
            $queryBuilder->createNamedParameter($value, Connection::PARAM_STR_ARRAY)
        );
    }
}

class_alias(UserLoginQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\User\UserLoginQueryBuilder');
