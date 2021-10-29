<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence;

/**
 * The field type interface which field types available to storage engines have to implement.
 *
 * @see \Ibexa\Contracts\Core\FieldType\FieldType
 */
interface FieldType
{
    /**
     * Returns the empty value for the field type that can be processed by the storage engine.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\FieldValue
     */
    public function getEmptyValue();
}

class_alias(FieldType::class, 'eZ\Publish\SPI\Persistence\FieldType');
