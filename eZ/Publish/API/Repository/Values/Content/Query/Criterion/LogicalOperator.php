<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Values\Content\Query\Criterion;

use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException;

/**
 * Note that the class should ideally have been in a Logical namespace, but it would have then be named 'And',
 * and 'And' is a PHP reserved word.
 */
abstract class LogicalOperator extends Criterion
{
    /**
     * The set of criteria combined by the logical operator.
     *
     * @var \eZ\Publish\API\Repository\Values\Content\Query\Criterion[]
     */
    public $criteria = [];

    /**
     * Creates a Logic operation with the given criteria.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion[] $criteria
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

    /**
     * @return array
     *
     * @deprecated in LogicalOperators since 7.2.
     * It will be removed in 8.0 when Logical Operator no longer extends Criterion.
     */
    public function getSpecifications(): array
    {
        @trigger_error('The ' . __METHOD__ . ' method is deprecated since version 7.2 and will be removed in 8.0.', E_USER_DEPRECATED);

        throw new NotImplementedException('getSpecifications() not implemented for LogicalOperators');
    }
}
