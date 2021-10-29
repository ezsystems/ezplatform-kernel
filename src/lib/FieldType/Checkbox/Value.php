<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Checkbox;

use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value for Checkbox field type.
 */
class Value extends BaseValue
{
    /**
     * Boolean value.
     *
     * @var bool
     */
    public $bool;

    /**
     * Construct a new Value object and initialize it $boolValue.
     *
     * @param bool $boolValue
     */
    public function __construct($boolValue = false)
    {
        $this->bool = $boolValue;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->bool ? '1' : '0';
    }
}

class_alias(Value::class, 'eZ\Publish\Core\FieldType\Checkbox\Value');
