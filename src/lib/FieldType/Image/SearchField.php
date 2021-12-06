<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Image;

use Ibexa\Contracts\Core\FieldType\Indexable;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;

/**
 * Indexable definition for TextLine field type.
 */
class SearchField implements Indexable
{
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition)
    {
        return [
            new Search\Field(
                'filename',
                $field->value->data['fileName'] ?? null,
                new Search\FieldType\StringField()
            ),
            new Search\Field(
                'alternative_text',
                $field->value->data['alternativeText'] ?? null,
                new Search\FieldType\StringField()
            ),
            new Search\Field(
                'file_size',
                $field->value->data['fileSize'] ?? null,
                new Search\FieldType\IntegerField()
            ),
            new Search\Field(
                'mime_type',
                $field->value->data['mime'] ?? null,
                new Search\FieldType\StringField()
            ),
        ];
    }

    public function getIndexDefinition()
    {
        return [
            'filename' => new Search\FieldType\StringField(),
            'alternative_text' => new Search\FieldType\StringField(),
            'file_size' => new Search\FieldType\IntegerField(),
            'mime_type' => new Search\FieldType\StringField(),
        ];
    }

    /**
     * Get name of the default field to be used for matching.
     *
     * As field types can index multiple fields (see MapLocation field type's
     * implementation of this interface), this method is used to define default
     * field for matching. Default field is typically used by Field criterion.
     *
     * @return string
     */
    public function getDefaultMatchField()
    {
        return 'filename';
    }

    /**
     * Get name of the default field to be used for sorting.
     *
     * As field types can index multiple fields (see MapLocation field type's
     * implementation of this interface), this method is used to define default
     * field for sorting. Default field is typically used by Field sort clause.
     *
     * @return string
     */
    public function getDefaultSortField()
    {
        return $this->getDefaultMatchField();
    }
}

class_alias(SearchField::class, 'eZ\Publish\Core\FieldType\Image\SearchField');
