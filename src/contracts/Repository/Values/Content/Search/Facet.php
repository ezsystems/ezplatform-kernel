<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Search;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * Base class for facets.
 *
 * @deprecated since eZ Platform 3.2.0, to be removed in eZ Platform 4.0.0.
 */
abstract class Facet extends ValueObject
{
    /**
     * The name of the facet.
     *
     * @var string
     */
    public $name;
}

class_alias(Facet::class, 'eZ\Publish\API\Repository\Values\Content\Search\Facet');
