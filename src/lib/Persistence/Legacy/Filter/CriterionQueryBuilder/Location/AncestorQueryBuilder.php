<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location;

use function array_filter;
use function array_map;
use function array_unique;
use function array_values;
use Doctrine\DBAL\Connection;
use function explode;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Ancestor;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use function trim;

/**
 * @internal for internal use by Repository Filtering
 */
final class AncestorQueryBuilder extends BaseLocationCriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof Ancestor;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Ancestor $criterion */
        parent::buildQueryConstraint($queryBuilder, $criterion);

        // extract numerical IDs from $criterion->value e.g. = ['/1/2/', '/1/4/10/']
        $locationIDs = array_merge(
            ...array_map(
                static function (string $pathString) {
                    return array_map(
                        'intval',
                        array_filter(explode('/', trim($pathString, '/')))
                    );
                },
                $criterion->value
            )
        );

        return $queryBuilder->expr()->in(
            'location.node_id',
            $queryBuilder->createNamedParameter(
                array_values(array_unique($locationIDs)),
                Connection::PARAM_INT_ARRAY
            )
        );
    }
}

class_alias(AncestorQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location\AncestorQueryBuilder');
