<?php

namespace eZ\Publish\Core\FieldType\User;

use eZ\Publish\SPI\FieldType\Indexable as IndexableInterface;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\SPI\Search;

final class SearchField implements IndexableInterface
{
    /**
     * @return \eZ\Publish\SPI\Search\Field[]
     */
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition): array
    {
        return [
            new Search\Field(
                'user_login',
                $field->value->externalData['login'],
                new Search\FieldType\StringField()
            ),
            new Search\Field(
                'user_email',
                $field->value->externalData['email'],
                new Search\FieldType\StringField()
            ),
        ];
    }

    /**
     * @return \eZ\Publish\SPI\Search\FieldType[]
     */
    public function getIndexDefinition(): array
    {
        return [
            'user_login' => new Search\FieldType\StringField(),
            'user_email' => new Search\FieldType\StringField(),
        ];
    }

    public function getDefaultMatchField(): string
    {
        return '';
    }

    public function getDefaultSortField(): string
    {
        return '';
    }
}
