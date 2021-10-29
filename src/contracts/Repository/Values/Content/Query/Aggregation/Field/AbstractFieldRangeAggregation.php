<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Field;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractRangeAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\FieldAggregation;

abstract class AbstractFieldRangeAggregation extends AbstractRangeAggregation implements FieldAggregation
{
    use FieldAggregationTrait;

    public function __construct(
        string $name,
        string $contentTypeIdentifier,
        string $fieldDefinitionIdentifier,
        array $ranges = []
    ) {
        parent::__construct($name, $ranges);

        $this->contentTypeIdentifier = $contentTypeIdentifier;
        $this->fieldDefinitionIdentifier = $fieldDefinitionIdentifier;
    }
}

class_alias(AbstractFieldRangeAggregation::class, 'eZ\Publish\API\Repository\Values\Content\Query\Aggregation\Field\AbstractFieldRangeAggregation');
