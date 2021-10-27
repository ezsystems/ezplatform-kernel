<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\Service\Mock;

use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value for TextLine field type.
 */
class ValueStub extends BaseValue
{
    /** @var string */
    public $value;

    /**
     * Construct a new Value object and initialize it $value.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}

class_alias(ValueStub::class, 'eZ\Publish\Core\Repository\Tests\Service\Mock\ValueStub');
