<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\SPI\Repository\Values\Filter;

use eZ\Publish\SPI\Persistence\Filter\Doctrine\FilteringQueryBuilder;

/**
 * Extension point to build filtering query for a given Criterion.
 *
 * Follows visitor pattern using buildQuery method to visit an implementation.
 */
interface CriterionQueryBuilder
{
    /**
     * Tag name to be used when defining Service in Dependency Injection Container.
     * Note that it's recommended to rely on auto-configuration instead.
     */
    public const SYMFONY_TAG_NAME = 'ezplatform.filter.criterion.query_builder';

    public function accepts(FilteringCriterion $criterion): bool;

    /**
     * Apply necessary Doctrine Query clauses & return part to be used for WHERE constraints.
     *
     * @return string|null string injected as WHERE constraints, null to skip injecting.
     */
    public function buildQueryConstraint(FilteringQueryBuilder $queryBuilder, FilteringCriterion $criterion): ?string;
}
