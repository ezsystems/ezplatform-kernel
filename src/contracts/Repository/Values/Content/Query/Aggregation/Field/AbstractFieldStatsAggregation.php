<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Field;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractStatsAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\FieldAggregation;

abstract class AbstractFieldStatsAggregation extends AbstractStatsAggregation implements FieldAggregation
{
    use FieldAggregationTrait;

    public function __construct(
        string $name,
        string $contentTypeIdentifier,
        string $fieldDefinitionIdentifier
    ) {
        parent::__construct($name);

        $this->contentTypeIdentifier = $contentTypeIdentifier;
        $this->fieldDefinitionIdentifier = $fieldDefinitionIdentifier;
    }
}

class_alias(AbstractFieldStatsAggregation::class, 'eZ\Publish\API\Repository\Values\Content\Query\Aggregation\Field\AbstractFieldStatsAggregation');
