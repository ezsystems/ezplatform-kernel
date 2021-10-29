<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;

/**
 * This class represents a field range facet.
 *
 * @deprecated since eZ Platform 3.2.0, to be removed in eZ Platform 4.0.0.
 */
class FieldRangeFacet extends Facet
{
    /**
     * Number of documents not containing any terms in the queried fields.
     *
     * @var int
     */
    public $missingCount;

    /**
     * The number of terms which are not in the queried top list.
     *
     * @var int
     */
    public $otherCount;

    /**
     * The total count of terms found.
     *
     * @var int
     */
    public $totalCount;

    /**
     * For each interval there is an entry with statistical data.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet\RangeFacetEntry[]
     */
    public $entries;
}

class_alias(FieldRangeFacet::class, 'eZ\Publish\API\Repository\Values\Content\Search\Facet\FieldRangeFacet');
