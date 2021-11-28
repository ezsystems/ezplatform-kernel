<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * Abstract class for all field value classes.
 * A field value object is to be understood with associated field type.
 */
abstract class Value extends ValueObject implements ValueInterface
{
    /**
     * Returns a string representation of the field value.
     *
     * @return string
     */
    abstract public function __toString();
}

class_alias(Value::class, 'eZ\Publish\Core\FieldType\Value');
