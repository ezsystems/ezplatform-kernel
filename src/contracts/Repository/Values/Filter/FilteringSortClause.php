<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Filter;

/**
 * Marker for Content & Location filtering Sort Clause.
 */
interface FilteringSortClause
{
}

class_alias(FilteringSortClause::class, 'eZ\Publish\SPI\Repository\Values\Filter\FilteringSortClause');
