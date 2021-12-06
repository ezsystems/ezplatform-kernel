<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common\FieldValueMapper;

use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\IdentifierField;
use Ibexa\Core\Search\Common\FieldValueMapper;

/**
 * Common identifier field value mapper implementation.
 */
class IdentifierMapper extends FieldValueMapper
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
        return $field->type instanceof IdentifierField;
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
        if ($field->type->raw) {
            return $field->value;
        }

        return $this->convert($field->value);
    }

    /**
     * Convert to a proper search engine representation.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function convert($value)
    {
        // Remove non-printable characters
        return preg_replace('([^A-Za-z0-9/]+)', '', $value);
    }
}

class_alias(IdentifierMapper::class, 'eZ\Publish\Core\Search\Common\FieldValueMapper\IdentifierMapper');
