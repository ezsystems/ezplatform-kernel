<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Url;

use Ibexa\Contracts\Core\FieldType\Indexable;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;

/**
 * Indexable definition for Url field type.
 */
class SearchField implements Indexable
{
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition)
    {
        return [
            new Search\Field(
                'value_url',
                $field->value->externalData,
                new Search\FieldType\StringField()
            ),
            new Search\Field(
                'value_id',
                isset($field->value->data['urlId']) ? $field->value->data['urlId'] : '',
                new Search\FieldType\StringField()
            ),
            new Search\Field(
                'value_text',
                $text = (isset($field->value->data['text']) ? $field->value->data['text'] : ''),
                new Search\FieldType\StringField()
            ),
            new Search\Field(
                'fulltext',
                $text,
                new Search\FieldType\FullTextField()
            ),
        ];
    }

    public function getIndexDefinition()
    {
        return [
            'value_url' => new Search\FieldType\StringField(),
            'value_id' => new Search\FieldType\StringField(),
            'value_text' => new Search\FieldType\StringField(),
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
        return 'value_url';
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

class_alias(SearchField::class, 'eZ\Publish\Core\FieldType\Url\SearchField');
