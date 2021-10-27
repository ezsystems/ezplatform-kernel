<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\FieldType\Generic\Stubs;

use Ibexa\Contracts\Core\FieldType\Generic\Type as BaseType;

final class Type extends BaseType
{
    public function getFieldTypeIdentifier(): string
    {
        return 'generic';
    }
}

class_alias(Type::class, 'eZ\Publish\SPI\FieldType\Generic\Tests\Stubs\Type');
