<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\FieldType;

/**
 * Interface for field value classes.
 */
interface Value
{
    /**
     * Returns a string representation of the field value.
     *
     * @return string
     */
    public function __toString();
}

class_alias(Value::class, 'eZ\Publish\SPI\FieldType\Value');
