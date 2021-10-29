<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Contracts\Core\Repository\Values\Trash\Query\Criterion as TrashCriterion;

/**
 * A NOT logical criterion.
 */
class LogicalNot extends LogicalOperator implements FilteringCriterion, TrashCriterion
{
    /**
     * Creates a new NOT logic criterion.
     *
     * Will match of the given criterion doesn't match
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[] $criteria One criterion, as an array
     *
     * @throws \InvalidArgumentException if more than one criterion is given in the array parameter
     */
    public function __construct(Criterion $criterion)
    {
        parent::__construct([$criterion]);
    }
}

class_alias(LogicalNot::class, 'eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot');
