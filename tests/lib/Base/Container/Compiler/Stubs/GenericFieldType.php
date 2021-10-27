<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Base\Container\Compiler\Stubs;

use Ibexa\Contracts\Core\FieldType\Generic\Type;

final class GenericFieldType extends Type
{
    public function getFieldTypeIdentifier(): string
    {
        return 'field_type_identifier';
    }
}

class_alias(GenericFieldType::class, 'eZ\Publish\Core\Base\Tests\Container\Compiler\Stubs\GenericFieldType');
