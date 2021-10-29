<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Url;

use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value for Url field type.
 */
class Value extends BaseValue
{
    /**
     * Link content.
     *
     * @var string|null
     */
    public $link;

    /**
     * Text content.
     *
     * @var string|null
     */
    public $text;

    /**
     * Construct a new Value object and initialize it with its $link and optional $text.
     *
     * @param string $link
     * @param string $text
     */
    public function __construct($link = null, $text = null)
    {
        $this->link = $link;
        $this->text = $text;
    }

    public function __toString()
    {
        return (string)$this->link;
    }
}

class_alias(Value::class, 'eZ\Publish\Core\FieldType\Url\Value');
