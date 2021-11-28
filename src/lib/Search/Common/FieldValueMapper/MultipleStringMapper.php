<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common\FieldValueMapper;

use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType;

/**
 * Common multiple string field value mapper implementation.
 */
class MultipleStringMapper extends StringMapper
{
    /**
     * Check if field can be mapped.
     *
     * @param \Ibexa\Contracts\Core\Search\Field $field
     *
     * @return bool
     */
    public function canMap(Field $field)
    {
        return
            $field->type instanceof FieldType\MultipleStringField ||
            $field->type instanceof FieldType\TextField ||
            $field->type instanceof FieldType\FullTextField;
    }

    /**
     * Map field value to a proper search engine representation.
     *
     * @param \Ibexa\Contracts\Core\Search\Field $field
     *
     * @return array
     */
    public function map(Field $field)
    {
        $values = [];

        foreach ((array)$field->value as $value) {
            $values[] = $this->convert($value);
        }

        return $values;
    }
}

class_alias(MultipleStringMapper::class, 'eZ\Publish\Core\Search\Common\FieldValueMapper\MultipleStringMapper');
