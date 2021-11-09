<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Filter;

/**
 * Marker for Content & Location filtering Criterion.
 */
interface FilteringCriterion
{
}

class_alias(FilteringCriterion::class, 'eZ\Publish\SPI\Repository\Values\Filter\FilteringCriterion');
