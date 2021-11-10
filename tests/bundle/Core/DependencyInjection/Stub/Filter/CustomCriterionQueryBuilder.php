<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Stub\Filter;

use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;

/**
 * Stub for {@see \Ibexa\Tests\Bundle\Core\DependencyInjection\IbexaCoreExtensionTest::testFilteringQueryBuildersAutomaticConfiguration}.
 */
class CustomCriterionQueryBuilder implements CriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return true;
    }

    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        return null;
    }
}

class_alias(CustomCriterionQueryBuilder::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Stub\Filter\CustomCriterionQueryBuilder');
