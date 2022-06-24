<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Search\Common\FieldValueMapper;

use eZ\Publish\Core\Search\Common\FieldValueMapper;
use eZ\Publish\SPI\Search\Field;
use eZ\Publish\SPI\Search\FieldType;

/**
 * Common string field value mapper implementation.
 */
class StringMapper extends FieldValueMapper
{
    public const REPLACE_WITH_SPACE_PATTERN = '([\x09\x0B\x0C]+)';
    public const REMOVE_PATTERN = '([\x00-\x08\x0E-\x1F]+)';

    public function canMap(Field $field): bool
    {
        return $field->getType() instanceof FieldType\StringField;
    }

    public function map(Field $field)
    {
        return $this->convert($field->getValue());
    }

    /**
     * Convert to a proper search engine representation.
     *
     * @param mixed $value
     */
    protected function convert($value): string
    {
        // Replace tab, vertical tab, form-feed chars to single space.
        $value = preg_replace(
            self::REPLACE_WITH_SPACE_PATTERN,
            ' ',
            (string)$value
        );

        // Remove non-printable characters.
        return preg_replace(
            self::REMOVE_PATTERN,
            '',
            (string)$value
        );
    }
}
