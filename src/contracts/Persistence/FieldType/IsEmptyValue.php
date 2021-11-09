<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\FieldType;

use Ibexa\Contracts\Core\Persistence\Content\FieldValue;

/**
 * The field type interface which field types available to storage engines have to implement.
 *
 * @see \Ibexa\Contracts\Core\FieldType\FieldType
 * @deprecated since 7.5.6. In 8.0 (for eZ Platform 3.0) it will be merged into the
 *             `\Ibexa\Contracts\Core\Persistence\FieldType` interface
 */
interface IsEmptyValue
{
    /**
     * Returns the empty value for the field type that can be processed by the storage engine.
     */
    public function isEmptyValue(FieldValue $fieldValue): bool;
}

class_alias(IsEmptyValue::class, 'eZ\Publish\SPI\Persistence\FieldType\IsEmptyValue');
