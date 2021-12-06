<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Visibility;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;

/**
 * @internal for internal use by Repository Filtering
 */
final class VisibilityQueryBuilder extends BaseLocationCriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof Visibility;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Visibility $criterion */
        parent::buildQueryConstraint($queryBuilder, $criterion);

        $expressionBuilder = $queryBuilder->expr();
        $columnsExpressions = $this->getVisibilityColumnsExpressions(
            $queryBuilder,
            $criterion->value[0]
        );

        return $criterion->value[0] === Visibility::VISIBLE
            ? (string)$expressionBuilder->andX(...$columnsExpressions)
            : (string)$expressionBuilder->orX(...$columnsExpressions);
    }

    private function getVisibilityColumnsExpressions(
        QueryBuilder $queryBuilder,
        int $visibleFlag
    ): array {
        $expressionBuilder = $queryBuilder->expr();

        return [
            $expressionBuilder->eq(
                'location.is_hidden',
                $queryBuilder->createNamedParameter($visibleFlag, ParameterType::INTEGER)
            ),
            $expressionBuilder->eq(
                'location.is_invisible',
                $queryBuilder->createNamedParameter($visibleFlag, ParameterType::INTEGER)
            ),
        ];
    }
}

class_alias(VisibilityQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location\VisibilityQueryBuilder');
