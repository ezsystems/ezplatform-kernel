<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\FieldType;

use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;

/**
 * The field type interface which all field types have to implement to be
 * indexable by search backends.
 */
interface Indexable
{
    /**
     * Get index data for field for search backend.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     * @param \Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition $fieldDefinition
     *
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition);

    /**
     * Get index field types for search backend.
     *
     * @return array<string, \Ibexa\Contracts\Core\Search\FieldType>
     */
    public function getIndexDefinition();

    /**
     * Get name of the default field to be used for matching.
     *
     * As field types can index multiple fields (see MapLocation field type's
     * implementation of this interface), this method is used to define default
     * field for matching. Default field is typically used by Field criterion.
     *
     * @return string
     */
    public function getDefaultMatchField();

    /**
     * Get name of the default field to be used for sorting.
     *
     * As field types can index multiple fields (see MapLocation field type's
     * implementation of this interface), this method is used to define default
     * field for sorting. Default field is typically used by Field sort clause.
     *
     * @return string
     */
    public function getDefaultSortField();
}

class_alias(Indexable::class, 'eZ\Publish\SPI\FieldType\Indexable');
