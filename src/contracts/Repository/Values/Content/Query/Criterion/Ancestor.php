<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator\Specifications;
use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use InvalidArgumentException;

/**
 * A criterion that matches content that is ancestor to the given Location path string.
 *
 * Content will be matched if it is part of at least one of the given subtree path strings.
 */
class Ancestor extends Criterion implements FilteringCriterion
{
    /**
     * Creates a new Ancestor criterion.
     *
     * @param string|string[] $value Location path string
     *
     * @throws \InvalidArgumentException if a non integer or string id is given
     * @throws \InvalidArgumentException if the value type doesn't match the operator
     */
    public function __construct($value)
    {
        foreach ((array)$value as $pathString) {
            if (preg_match('/^(\/\w+)+\/$/', $pathString) !== 1) {
                throw new InvalidArgumentException(
                    "'$pathString' value must follow the pathString format, e.g. '/1/2/'"
                );
            }
        }

        parent::__construct(null, null, $value);
    }

    public function getSpecifications(): array
    {
        return [
            new Specifications(
                Operator::EQ,
                Specifications::FORMAT_SINGLE,
                Specifications::TYPE_STRING
            ),
            new Specifications(
                Operator::IN,
                Specifications::FORMAT_ARRAY,
                Specifications::TYPE_STRING
            ),
        ];
    }
}

class_alias(Ancestor::class, 'eZ\Publish\API\Repository\Values\Content\Query\Criterion\Ancestor');
