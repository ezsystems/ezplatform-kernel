<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Values\URL\Query\Criterion;

use eZ\Publish\API\Repository\Values\URL\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException;

abstract class LogicalOperator extends Criterion
{
    /**
     * The set of criteria combined by the logical operator.
     *
     * @var \eZ\Publish\API\Repository\Values\URL\Query\Criterion[]
     */
    public $criteria = [];

    /**
     * Creates a Logic operation with the given criteria.
     *
     * @param \eZ\Publish\API\Repository\Values\URL\Query\Criterion[] $criteria
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException
     */
    public function __construct(array $criteria)
    {
        foreach ($criteria as $key => $criterion) {
            if (!$criterion instanceof Criterion) {
                throw new InvalidCriterionArgumentException($key, $criterion, Criterion::class);
            }

            $this->criteria[] = $criterion;
        }
    }
}
