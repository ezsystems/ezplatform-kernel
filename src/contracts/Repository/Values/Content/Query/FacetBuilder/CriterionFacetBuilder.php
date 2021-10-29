<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;

/**
 * Build a criterion facet.
 *
 * If provided the search service returns a CriterionFacet based on the criterion provided
 * in the FacetBuilder class.
 *
 * @deprecated since eZ Platform 3.2.0, to be removed in eZ Platform 4.0.0.
 */
class CriterionFacetBuilder extends FacetBuilder
{
}

class_alias(CriterionFacetBuilder::class, 'eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder\CriterionFacetBuilder');
