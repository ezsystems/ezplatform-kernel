<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter;

use Ibexa\Contracts\Core\Persistence\Filter\CriterionVisitor as FilteringCriterionVisitor;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use function sprintf;

/**
 * @internal Type-hint {@see \Ibexa\Contracts\Core\Persistence\Filter\CriterionVisitor} instead
 */
final class CriterionVisitor implements FilteringCriterionVisitor
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder[] */
    private $criterionQueryBuilders;

    public function __construct(iterable $criterionQueryBuilders)
    {
        $this->setCriterionQueryBuilders($criterionQueryBuilders);
    }

    public function setCriterionQueryBuilders(iterable $criterionQueryBuilders): void
    {
        $this->criterionQueryBuilders = $criterionQueryBuilders;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException if there's no builder for a criterion
     */
    public function visitCriteria(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): string {
        foreach ($this->criterionQueryBuilders as $criterionQueryBuilder) {
            if ($criterionQueryBuilder->accepts($criterion)) {
                return $criterionQueryBuilder->buildQueryConstraint(
                    $queryBuilder,
                    $criterion
                );
            }
        }

        throw new NotImplementedException(
            sprintf(
                'There is no Filtering Criterion Query Builder for %s Criterion',
                get_class($criterion)
            )
        );
    }
}

class_alias(CriterionVisitor::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionVisitor');
