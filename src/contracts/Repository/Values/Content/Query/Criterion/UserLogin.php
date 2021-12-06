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

class UserLogin extends Criterion implements FilteringCriterion
{
    /**
     * @param string|string[] $value
     * @param string|null $operator
     */
    public function __construct($value, ?string $operator = null)
    {
        parent::__construct(null, $operator, $value);
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator\Specifications[]
     */
    public function getSpecifications(): array
    {
        return [
            new Specifications(
                Operator::EQ,
                Specifications::FORMAT_SINGLE
            ),
            new Specifications(
                Operator::IN,
                Specifications::FORMAT_ARRAY
            ),
            new Specifications(
                Operator::LIKE,
                Specifications::FORMAT_SINGLE
            ),
        ];
    }
}

class_alias(UserLogin::class, 'eZ\Publish\API\Repository\Values\Content\Query\Criterion\UserLogin');
