<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

/**
 * This is the base class for Location criterions.
 */
abstract class Location extends Criterion
{
}

class_alias(Location::class, 'eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location');
