<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;

final class ContentTypeTermAggregation extends AbstractTermAggregation
{
}

class_alias(ContentTypeTermAggregation::class, 'eZ\Publish\API\Repository\Values\Content\Query\Aggregation\ContentTypeTermAggregation');
