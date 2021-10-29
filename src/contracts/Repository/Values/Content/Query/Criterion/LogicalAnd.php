<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Contracts\Core\Repository\Values\Trash\Query\Criterion as TrashCriterion;

/**
 * This criterion implements a logical AND criterion and will only match
 * if ALL of the given criteria match.
 */
class LogicalAnd extends LogicalOperator implements TrashCriterion, FilteringCriterion
{
}

class_alias(LogicalAnd::class, 'eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd');
