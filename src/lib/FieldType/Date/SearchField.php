<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Date;

use DateTime;
use Ibexa\Contracts\Core\FieldType\Indexable;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;

/**
 * Indexable definition for Date field type.
 */
class SearchField implements Indexable
{
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition)
    {
        if ($field->value->data !== null) {
            $dateTime = new DateTime();
            $dateTime->setTimestamp($field->value->data['timestamp']);

            $value = $dateTime->format('Y-m-d\\Z');
        } else {
            $value = null;
        }

        return [
            new Search\Field(
                'value',
                $value,
                new Search\FieldType\DateField()
            ),
        ];
    }

    public function getIndexDefinition()
    {
        return [
            'value' => new Search\FieldType\DateField(),
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
        return 'value';
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

class_alias(SearchField::class, 'eZ\Publish\Core\FieldType\Date\SearchField');
