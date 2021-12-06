<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location;

use function array_map;
use Doctrine\DBAL\ParameterType;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;

/**
 * @internal for internal use by Repository Filtering
 */
final class SubtreeQueryBuilder extends BaseLocationCriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof Subtree;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree $criterion */
        parent::buildQueryConstraint($queryBuilder, $criterion);

        $expressionBuilder = $queryBuilder->expr();
        $statements = array_map(
            static function (string $pathString) use ($queryBuilder, $expressionBuilder): string {
                return $expressionBuilder->like(
                    'location.path_string',
                    $queryBuilder->createNamedParameter($pathString . '%', ParameterType::STRING)
                );
            },
            $criterion->value
        );

        return (string)$expressionBuilder->orX(...$statements);
    }
}

class_alias(SubtreeQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location\SubtreeQueryBuilder');
