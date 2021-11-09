<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Integer;

use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value for Integer field type.
 */
class Value extends BaseValue
{
    /**
     * Content of the value.
     *
     * @var int|null
     */
    public $value;

    /**
     * Construct a new Value object and initialize with $value.
     *
     * @param int|null $value
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

class_alias(Value::class, 'eZ\Publish\Core\FieldType\Integer\Value');
