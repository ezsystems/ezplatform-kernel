<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Keyword;

use Ibexa\Contracts\Core\FieldType\Indexable;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;

/**
 * Indexable definition for Keyword field type.
 */
class SearchField implements Indexable
{
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition)
    {
        return [
            new Search\Field(
                'value',
                $field->value->externalData,
                new Search\FieldType\MultipleStringField()
            ),
            new Search\Field(
                'sort_value',
                implode(' ', $field->value->externalData),
                new Search\FieldType\StringField()
            ),
            new Search\Field(
                'fulltext',
                $field->value->externalData,
                new Search\FieldType\FullTextField()
            ),
        ];
    }

    public function getIndexDefinition()
    {
        return [
            'value' => new Search\FieldType\MultipleStringField(),
            'sort_value' => new Search\FieldType\StringField(),
        ];
    }

    public function getDefaultMatchField()
    {
        return 'value';
    }

    public function getDefaultSortField()
    {
        return 'sort_value';
    }
}

class_alias(SearchField::class, 'eZ\Publish\Core\FieldType\Keyword\SearchField');
