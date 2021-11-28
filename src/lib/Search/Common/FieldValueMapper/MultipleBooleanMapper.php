<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common\FieldValueMapper;

use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\MultipleBooleanField;
use Ibexa\Core\Search\Common\FieldValueMapper;

/**
 * Common multiple boolean field value mapper implementation.
 */
class MultipleBooleanMapper extends FieldValueMapper
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
        return $field->type instanceof MultipleBooleanField;
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

        foreach ((array)$field->value as $value) {
            $values[] = (bool)$value;
        }

        return $values;
    }
}

class_alias(MultipleBooleanMapper::class, 'eZ\Publish\Core\Search\Common\FieldValueMapper\MultipleBooleanMapper');
