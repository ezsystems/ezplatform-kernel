<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\TextLine;

use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value for TextLine field type.
 */
class Value extends BaseValue
{
    /**
     * Text content.
     *
     * @var string
     */
    public $text;

    /**
     * Construct a new Value object and initialize it $text.
     *
     * @param string $text
     */
    public function __construct($text = '')
    {
        $this->text = $text;
    }

    public function __toString()
    {
        return (string)$this->text;
    }
}

class_alias(Value::class, 'eZ\Publish\Core\FieldType\TextLine\Value');
