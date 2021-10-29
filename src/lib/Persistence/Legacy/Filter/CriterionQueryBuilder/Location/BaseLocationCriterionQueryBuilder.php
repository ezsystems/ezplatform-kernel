<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location;

use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;

/**
 * @internal for internal use by Repository Filtering
 */
abstract class BaseLocationCriterionQueryBuilder implements CriterionQueryBuilder
{
    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        $queryBuilder->joinAllLocations();

        return null;
    }
}

class_alias(BaseLocationCriterionQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location\BaseLocationCriterionQueryBuilder');
