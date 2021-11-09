<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\FieldType;

interface Comparable
{
    public function valuesEqual(Value $value1, Value $value2): bool;
}

class_alias(Comparable::class, 'eZ\Publish\SPI\FieldType\Comparable');
