<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\Content;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class Field extends ValueObject
{
    /**
     * Field ID.
     *
     * @var int
     */
    public $id;

    /**
     * Corresponding field definition.
     *
     * @var int
     */
    public $fieldDefinitionId;

    /**
     * Data type name.
     *
     * @var string
     */
    public $type;

    /**
     * Value of the field.
     *
     * @var \Ibexa\Contracts\Core\Persistence\Content\FieldValue
     */
    public $value;

    /**
     * Language code of this Field.
     *
     * @var string
     */
    public $languageCode;

    /**
     * @var int|null Null if not created yet
     *
     * @todo Normally we would use a create struct here
     */
    public $versionNo;

    /**
     * Clone object properties.
     *
     * Note: `clone` keyword performs shallow copy of an object.
     * For properties being objects this means that a reference
     * is copied instead of the actual object.
     */
    public function __clone()
    {
        $this->value = clone $this->value;
    }
}

class_alias(Field::class, 'eZ\Publish\SPI\Persistence\Content\Field');
