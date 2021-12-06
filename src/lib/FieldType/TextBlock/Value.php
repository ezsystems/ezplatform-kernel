<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\TextBlock;

use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;

/**
 * Value for TextBlock field type.
 */
class Value extends TextLineValue
{
}

class_alias(Value::class, 'eZ\Publish\Core\FieldType\TextBlock\Value');
