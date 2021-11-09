<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common\FieldValueMapper;

use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\MultipleIntegerField;

/**
 * Common multiple integer field value mapper implementation.
 */
class MultipleIntegerMapper extends IntegerMapper
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
        return $field->type instanceof MultipleIntegerField;
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

class_alias(MultipleIntegerMapper::class, 'eZ\Publish\Core\Search\Common\FieldValueMapper\MultipleIntegerMapper');
