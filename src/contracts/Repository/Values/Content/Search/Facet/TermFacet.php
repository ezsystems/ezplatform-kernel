<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;

/**
 * this class hold counts for content in sections.
 *
 * @deprecated since eZ Platform 3.2.0, to be removed in eZ Platform 4.0.0.
 */
class TermFacet extends Facet
{
    /**
     * An array with term as key and count of matching content objects as value.
     *
     * @var array
     */
    public $entries;
}

class_alias(TermFacet::class, 'eZ\Publish\API\Repository\Values\Content\Search\Facet\TermFacet');
