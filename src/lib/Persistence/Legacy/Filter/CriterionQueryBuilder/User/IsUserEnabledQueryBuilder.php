<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\User;

use Doctrine\DBAL\ParameterType;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\IsUserEnabled;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Core\FieldType\User\UserStorage\Gateway\DoctrineStorage;

/**
 * @internal for internal use by Repository Filtering
 */
final class IsUserEnabledQueryBuilder extends BaseUserCriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof IsUserEnabled;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\IsUserEnabled $criterion */
        parent::buildQueryConstraint($queryBuilder, $criterion);

        $queryBuilder->joinOnce(
            'user_storage',
            DoctrineStorage::USER_SETTING_TABLE,
            'user_settings',
            'user_storage.contentobject_id = user_settings.user_id'
        );

        return $queryBuilder->expr()->eq(
            'user_settings.is_enabled',
            $queryBuilder->createNamedParameter(
                (int)reset($criterion->value),
                ParameterType::INTEGER
            )
        );
    }
}

class_alias(IsUserEnabledQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\User\IsUserEnabledQueryBuilder');
