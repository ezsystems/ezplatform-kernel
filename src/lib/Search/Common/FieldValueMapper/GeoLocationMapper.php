<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common\FieldValueMapper;

use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\GeoLocationField;
use Ibexa\Core\Search\Common\FieldValueMapper;

/**
 * Common geo location field value mapper implementation.
 */
class GeoLocationMapper extends FieldValueMapper
{
    /**
     * Check if field can be mapped.
     *
     * @param \Ibexa\Contracts\Core\Search\Field $field
     *
     * @return bool
     */
    public function canMap(Field $field)
    {
        return $field->type instanceof GeoLocationField;
    }

    /**
     * Map field value to a proper search engine representation.
     *
     * @param \Ibexa\Contracts\Core\Search\Field $field
     *
     * @return mixed|null Returns null on empty value
     */
    public function map(Field $field)
    {
        if ($field->value['latitude'] === null || $field->value['longitude'] === null) {
            return null;
        }

        return sprintf('%F,%F', $field->value['latitude'], $field->value['longitude']);
    }
}

class_alias(GeoLocationMapper::class, 'eZ\Publish\Core\Search\Common\FieldValueMapper\GeoLocationMapper');
