<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Null;

use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value for Null field type.
 */
class Value extends BaseValue
{
    /**
     * Content of the value.
     *
     * @var mixed
     */
    public $value = null;

    /**
     * Construct a new Value object and initialize with $value.
     *
     * @param int $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}

class_alias(Value::class, 'eZ\Publish\Core\FieldType\Null\Value');
