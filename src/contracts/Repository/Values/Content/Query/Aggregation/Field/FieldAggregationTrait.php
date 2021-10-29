<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Field;

trait FieldAggregationTrait
{
    /** @var string */
    public $contentTypeIdentifier;

    /** @var string */
    public $fieldDefinitionIdentifier;

    public function getContentTypeIdentifier(): string
    {
        return $this->contentTypeIdentifier;
    }

    public function getFieldDefinitionIdentifier(): string
    {
        return $this->fieldDefinitionIdentifier;
    }
}

class_alias(FieldAggregationTrait::class, 'eZ\Publish\API\Repository\Values\Content\Query\Aggregation\Field\FieldAggregationTrait');
