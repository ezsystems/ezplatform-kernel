<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content;

/**
 * This class is used to perform a Location query.
 */
class LocationQuery extends Query
{
}

class_alias(LocationQuery::class, 'eZ\Publish\API\Repository\Values\Content\LocationQuery');
