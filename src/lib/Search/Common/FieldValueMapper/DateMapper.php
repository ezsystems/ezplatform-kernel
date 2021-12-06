<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common\FieldValueMapper;

use DateTime;
use Exception;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Contracts\Core\Search\FieldType\DateField;
use Ibexa\Core\Search\Common\FieldValueMapper;
use InvalidArgumentException;

/**
 * Common date field value mapper implementation.
 */
class DateMapper extends FieldValueMapper
{
    /**
     * Check if field can be mapped.
     *
     * @param \Ibexa\Contracts\Core\Search\Field $field
     *
     * @return mixed
     */
    public function canMap(Field $field)
    {
        return $field->type instanceof DateField;
    }

    /**
     * Map field value to a proper search engine representation.
     *
     * @param \Ibexa\Contracts\Core\Search\Field $field
     *
     * @return mixed
     */
    public function map(Field $field)
    {
        if (is_numeric($field->value)) {
            $date = new DateTime("@{$field->value}");
        } else {
            try {
                $date = new DateTime($field->value);
            } catch (Exception $e) {
                throw new InvalidArgumentException('Invalid date provided: ' . $field->value);
            }
        }

        return $date->format('Y-m-d\\TH:i:s\\Z');
    }
}

class_alias(DateMapper::class, 'eZ\Publish\Core\Search\Common\FieldValueMapper\DateMapper');
