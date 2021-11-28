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

/**
 * @internal for internal use by Repository Filtering
 */
final class ModifierQueryBuilder implements CriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof UserMetadata && $criterion->target === UserMetadata::MODIFIER;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\UserMetadata $criterion */
        $value = (array)$criterion->value;

        return $queryBuilder->expr()->in(
            'version.creator_id',
            $queryBuilder->createNamedParameter($value, Connection::PARAM_INT_ARRAY)
        );
    }
}

class_alias(ModifierQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\User\Metadata\ModifierQueryBuilder');
