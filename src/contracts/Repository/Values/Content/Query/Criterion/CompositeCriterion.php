<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

abstract class CompositeCriterion extends Criterion
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion */
    public $criteria;

    public function __construct(Criterion $criteria)
    {
        $this->criteria = $criteria;
    }

    public function getSpecifications(): array
    {
        throw new NotImplementedException('getSpecifications() not implemented for CompositeCriterion');
    }
}

class_alias(CompositeCriterion::class, 'eZ\Publish\API\Repository\Values\Content\Query\Criterion\CompositeCriterion');
