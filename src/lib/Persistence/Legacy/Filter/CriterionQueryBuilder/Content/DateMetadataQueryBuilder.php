<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content;

use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\DateMetadata;
use Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;

/**
 * @internal for internal use by Repository Filtering
 */
final class DateMetadataQueryBuilder implements CriterionQueryBuilder
{
    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof DateMetadata;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\DateMetadata $criterion */
        $column = $criterion->target === DateMetadata::MODIFIED ? 'modified' : 'published';
        $column = "content.{$column}";

        $value = (array)$criterion->value;

        return $queryBuilder->buildOperatorBasedCriterionConstraint(
            $column,
            $value,
            $criterion->operator
        );
    }
}

class_alias(DateMetadataQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Content\DateMetadataQueryBuilder');
