<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Location;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\LocationAggregation;

final class LocationChildrenTermAggregation extends AbstractTermAggregation implements LocationAggregation
{
}

class_alias(LocationChildrenTermAggregation::class, 'eZ\Publish\API\Repository\Values\Content\Query\Aggregation\Location\LocationChildrenTermAggregation');
