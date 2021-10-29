<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;

/**
 * This class represents a date range facet holding counts for content in the built date ranges.
 *
 * @deprecated since eZ Platform 3.2.0, to be removed in eZ Platform 4.0.0.
 */
class DateRangeFacet extends Facet
{
    /**
     * The date intervals with statistical data.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet\RangeFacetEntry
     */
    public $entries;
}

class_alias(DateRangeFacet::class, 'eZ\Publish\API\Repository\Values\Content\Search\Facet\DateRangeFacet');
