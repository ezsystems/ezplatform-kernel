<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;

/**
 * Marker interface for location based aggregation.
 */
interface LocationAggregation
{
}

class_alias(LocationAggregation::class, 'eZ\Publish\API\Repository\Values\Content\Query\Aggregation\LocationAggregation');
