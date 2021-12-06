<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

/**
 * This is the base for Location SortClause classes, used to set sorting of Location queries.
 */
abstract class Location extends SortClause
{
}

class_alias(Location::class, 'eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location');
