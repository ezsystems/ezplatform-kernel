<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Persistence\Filter;

use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;

/**
 * @internal for internal use by Repository Filtering.
 * Visits instances of {@see \Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder}.
 */
interface CriterionVisitor
{
    public function visitCriteria(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): string;
}

class_alias(CriterionVisitor::class, 'eZ\Publish\SPI\Persistence\Filter\CriterionVisitor');
