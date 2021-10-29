<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;

/**
 * This is the base class for Location facet builders.
 *
 * @deprecated since eZ Platform 3.2.0, to be removed in eZ Platform 4.0.0.
 */
abstract class Location extends FacetBuilder
{
}

class_alias(Location::class, 'eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder\Location');
