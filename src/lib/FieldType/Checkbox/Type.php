<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Checkbox;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Checkbox field type.
 *
 * Represent boolean values.
 */
class Type extends FieldType
{
    /**
     * Returns the field type identifier for this field type.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return 'ezboolean';
    }

    /**
     * @param \Ibexa\Core\FieldType\Checkbox\Value|\Ibexa\Contracts\Core\FieldType\Value $value
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        return $value->bool ? '1' : '0';
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \Ibexa\Core\FieldType\Checkbox\Value
     */
    public function getEmptyValue()
    {
        return new Value(false);
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param bool|\Ibexa\Core\FieldType\Checkbox\Value $inputValue
     *
     * @return \Ibexa\Core\FieldType\Checkbox\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput($inputValue)
    {
        if (is_bool($inputValue)) {
            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \Ibexa\Core\FieldType\Checkbox\Value $value
     */
    protected function checkValueStructure(BaseValue $value)
    {
        if (!$value instanceof Value) {
            throw new InvalidArgumentType(
                '$value',
                Value::class,
                $value
            );
        }

        if (!is_bool($value->bool)) {
            throw new InvalidArgumentType(
                '$value->bool',
                'boolean',
                $value->bool
            );
        }
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @param \Ibexa\Core\FieldType\Checkbox\Value $value
     *
     * @return int
     */
    protected function getSortInfo(BaseValue $value)
    {
        return (int)$value->bool;
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return \Ibexa\Core\FieldType\Checkbox\Value $value
     */
    public function fromHash($hash)
    {
        return new Value($hash);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \Ibexa\Core\FieldType\Checkbox\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        return $value->bool;
    }

    /**
     * Returns whether the field type is searchable.
     *
     * @return bool
     */
    public function isSearchable()
    {
        return true;
    }
}

class_alias(Type::class, 'eZ\Publish\Core\FieldType\Checkbox\Type');
