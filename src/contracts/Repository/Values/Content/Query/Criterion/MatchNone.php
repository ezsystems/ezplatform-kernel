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
 * A criterion that just matches nothing.
 *
 * Useful for BlockingLimitation type, where a limitation is typically missing and needs to
 * tell the system should block everything within the OR conditions it might be part of.
 */
class MatchNone extends Criterion implements FilteringCriterion, TrashCriterion
{
    public function __construct()
    {
        // Do NOT call parent constructor. It tries to be too smart.
    }

    public function getSpecifications(): array
    {
        return [];
    }
}

class_alias(MatchNone::class, 'eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchNone');
