<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common\FieldValueMapper;

use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\MultipleIdentifierField;

/**
 * Common multiple identifier field value mapper implementation.
 */
class MultipleIdentifierMapper extends IdentifierMapper
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
        return $field->type instanceof MultipleIdentifierField;
    }

    /**
     * Map field value to a proper search engine representation.
     *
     * @param \Ibexa\Contracts\Core\Search\Field $field
     *
     * @return mixed
     */
    public function map(Field $field)
    {
        $values = [];

        foreach ($field->value as $value) {
            if (!$field->type->raw) {
                $value = $this->convert($value);
            }

            $values[] = $value;
        }

        return $values;
    }
}

class_alias(MultipleIdentifierMapper::class, 'eZ\Publish\Core\Search\Common\FieldValueMapper\MultipleIdentifierMapper');
