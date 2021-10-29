<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\ISBN;

use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value for ISBN field type.
 */
class Value extends BaseValue
{
    /**
     * ISBN content.
     *
     * @var string
     */
    public $isbn;

    /**
     * Construct a new Value object and initialize it with its $isbn.
     *
     * @param string $isbn
     */
    public function __construct($isbn = '')
    {
        $this->isbn = $isbn;
    }

    public function __toString()
    {
        return (string)$this->isbn;
    }
}

class_alias(Value::class, 'eZ\Publish\Core\FieldType\ISBN\Value');
