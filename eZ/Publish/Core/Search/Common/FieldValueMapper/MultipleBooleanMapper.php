<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Search\Common\FieldValueMapper;

use eZ\Publish\Core\Search\Common\FieldValueMapper;
use eZ\Publish\SPI\Search\Field;
use eZ\Publish\SPI\Search\FieldType\MultipleBooleanField;

/**
 * Common multiple boolean field value mapper implementation.
 */
class MultipleBooleanMapper extends FieldValueMapper
{
    public function canMap(Field $field): bool
    {
        return $field->getType() instanceof MultipleBooleanField;
    }

    public function map(Field $field)
    {
        $values = [];

        foreach ((array)$field->getValue() as $value) {
            $values[] = (bool)$value;
        }

        return $values;
    }
}
