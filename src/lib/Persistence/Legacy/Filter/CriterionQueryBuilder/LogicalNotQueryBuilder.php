<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder;

use Ibexa\Contracts\Core\Persistence\Filter\CriterionVisitor;
use Ibexa\Contracts\Core\Persistence\Filter\Doctrine\FilteringQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalNot;
use Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

/**
 * @internal for internal use by Repository Filtering
 */
final class LogicalNotQueryBuilder implements CriterionQueryBuilder
{
    /** @var \Ibexa\Contracts\Core\Persistence\Filter\CriterionVisitor */
    private $criterionVisitor;

    public function __construct(CriterionVisitor $criterionVisitor)
    {
        $this->criterionVisitor = $criterionVisitor;
    }

    public function accepts(FilteringCriterion $criterion): bool
    {
        return $criterion instanceof LogicalNot;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function buildQueryConstraint(
        FilteringQueryBuilder $queryBuilder,
        FilteringCriterion $criterion
    ): ?string {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalNot $criterion */
        if (!$criterion->criteria[0] instanceof FilteringCriterion) {
            throw new InvalidArgumentException(
                '$criterion',
                sprintf(
                    'Criterion needs to be a Filtering Criterion, got "%s"',
                    get_class($criterion->criteria[0])
                )
            );
        }

        $constraint = $this->criterionVisitor->visitCriteria(
            $queryBuilder,
            $criterion->criteria[0]
        );

        return null !== $constraint ? sprintf('NOT (%s)', $constraint) : null;
    }
}

class_alias(LogicalNotQueryBuilder::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\LogicalNotQueryBuilder');
