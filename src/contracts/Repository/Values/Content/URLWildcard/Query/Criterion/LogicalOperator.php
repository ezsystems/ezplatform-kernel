<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard\Query\Criterion;
use InvalidArgumentException;

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
     * @throws \InvalidArgumentException
     */
    public function __construct(array $criteria)
    {
        foreach ($criteria as $key => $criterion) {
            if (!$criterion instanceof Criterion) {
                if ($criterion === null) {
                    $type = 'null';
                } elseif (is_object($criterion)) {
                    $type = get_class($criterion);
                } elseif (is_array($criterion)) {
                    $type = 'Array, with keys: ' . implode(', ', array_keys($criterion));
                } else {
                    $type = gettype($criterion) . ", with value: '{$criterion}'";
                }

                throw new InvalidArgumentException(
                    "You provided {$type} at index '{$key}', but only Criterion objects are accepted"
                );
            }

            $this->criteria[] = $criterion;
        }
    }
}
