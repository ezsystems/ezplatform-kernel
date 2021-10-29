<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\Content;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class FieldTypeConstraints extends ValueObject
{
    /**
     * Validator settings compatible to the corresponding FieldType.
     *
     * This property contains validator settings as defined by the fields type.
     * Note that contents of this property must be serializable and exportable
     * (i.e. no circular references, resources and friends).
     *
     * @see \Ibexa\Contracts\Core\FieldType\FieldType
     *
     * @var mixed
     */
    public $validators;

    /**
     * Field settings compatible to the corresponding FieldType.
     *
     * This property contains field settings as defined by the fields type.
     * Note that contents of this property must be serializable and exportable
     * (i.e. no circular references, resources and friends).
     *
     * @see \Ibexa\Contracts\Core\FieldType\FieldType
     *
     * @var mixed
     */
    public $fieldSettings;
}

class_alias(FieldTypeConstraints::class, 'eZ\Publish\SPI\Persistence\Content\FieldTypeConstraints');
