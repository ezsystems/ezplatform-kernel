<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;

abstract class LogicalOperator implements Criterion
{
    /**
     * The set of criteria combined by the logical operator.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion[]
     */
    public $criteria = [];

    /**
     * Creates a Logic operation with the given criteria.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion[] $criteria
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException
     */
    public function __construct(array $criteria)
    {
        foreach ($criteria as $key => $criterion) {
            if (!$criterion instanceof Criterion) {
                throw new InvalidCriterionArgumentException($key, $criterion);
            }

            $this->criteria[] = $criterion;
        }
    }
}
