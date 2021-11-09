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

class SectionIdentifier extends Criterion implements FilteringCriterion
{
    /**
     * @param string|string[] $value
     */
    public function __construct($value)
    {
        parent::__construct(null, null, $value);
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator\Specifications[]
     */
    public function getSpecifications(): array
    {
        return [
            new Specifications(
                Operator::IN,
                Specifications::FORMAT_ARRAY
            ),
            new Specifications(
                Operator::EQ,
                Specifications::FORMAT_SINGLE
            ),
        ];
    }
}

class_alias(SectionIdentifier::class, 'eZ\Publish\API\Repository\Values\Content\Query\Criterion\SectionIdentifier');
