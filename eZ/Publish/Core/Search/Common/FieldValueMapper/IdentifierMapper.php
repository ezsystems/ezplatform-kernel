<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Search\Common\FieldValueMapper;

use eZ\Publish\Core\Search\Common\FieldValueMapper;
use eZ\Publish\SPI\Search\Field;
use eZ\Publish\SPI\Search\FieldType\IdentifierField;

/**
 * Common identifier field value mapper implementation.
 */
class IdentifierMapper extends FieldValueMapper
{
    public function canMap(Field $field): bool
    {
        return $field->getType() instanceof IdentifierField;
    }

    /**
     * Map field value to a proper search engine representation.
     *
     * @param \eZ\Publish\SPI\Search\Field $field
     *
     * @return mixed
     */
    public function map(Field $field)
    {
        if ($field->getType()->raw) {
            return $field->getValue();
        }

        return $this->convert($field->getValue());
    }

    /**
     * Convert to a proper search engine representation.
     *
     * @param mixed $value
     */
    protected function convert($value): string
    {
        // Remove non-printable characters
        return preg_replace('([^A-Za-z0-9/]+)', '', $value);
    }
}
