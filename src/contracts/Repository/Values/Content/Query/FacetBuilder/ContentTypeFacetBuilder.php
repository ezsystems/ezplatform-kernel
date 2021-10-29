<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;

/**
 * Building a content type facet.
 *
 * If provided the search service returns a ContentTypeFacet
 *
 * @deprecated since eZ Platform 3.2.0, to be removed in eZ Platform 4.0.0.
 */
class ContentTypeFacetBuilder extends FacetBuilder
{
}

class_alias(ContentTypeFacetBuilder::class, 'eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder\ContentTypeFacetBuilder');
