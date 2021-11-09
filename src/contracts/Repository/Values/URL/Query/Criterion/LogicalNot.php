<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;

class LogicalNot extends LogicalOperator
{
    /**
     * Creates a new NOT logic criterion.
     *
     * Will match of the given criterion doesn't match
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion $criterion criterion
     *
     * @throws \InvalidArgumentException if more than one criterion is given in the array parameter
     */
    public function __construct(Criterion $criterion)
    {
        parent::__construct([$criterion]);
    }
}

class_alias(LogicalNot::class, 'eZ\Publish\API\Repository\Values\URL\Query\Criterion\LogicalNot');
