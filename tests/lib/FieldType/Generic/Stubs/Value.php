<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\FieldType\Generic\Stubs;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;

final class Value implements ValueInterface
{
    private $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}

class_alias(Value::class, 'eZ\Publish\SPI\FieldType\Generic\Tests\Stubs\Value');
