<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Null;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * ATTENTION: For testing purposes only!
 */
class Type extends FieldType
{
    /**
     * Identifier for the field type this stuff is mocking.
     *
     * @var string
     */
    protected $fieldTypeIdentifier;

    /**
     * @param string $fieldTypeIdentifier
     */
    public function __construct($fieldTypeIdentifier)
    {
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
    }

    /**
     * Returns the field type identifier for this field type.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return $this->fieldTypeIdentifier;
    }

    /**
     * @param \Ibexa\Core\FieldType\Null\Value|\Ibexa\Contracts\Core\FieldType\Value $value
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        return (string)$value->value;
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \Ibexa\Core\FieldType\Null\Value
     */
    public function getEmptyValue()
    {
        return new Value(null);
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param \Ibexa\Core\FieldType\Null\Value $inputValue
     *
     * @return \Ibexa\Core\FieldType\Null\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput($inputValue)
    {
        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \Ibexa\Core\FieldType\Null\Value $value
     */
    protected function checkValueStructure(BaseValue $value)
    {
        // Does nothing
    }

    /**
     * {@inheritdoc}
     */
    protected function getSortInfo(BaseValue $value)
    {
        return null;
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return \Ibexa\Core\FieldType\Null\Value $value
     */
    public function fromHash($hash)
    {
        return new Value($hash);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \Ibexa\Core\FieldType\Null\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        if (isset($value->value)) {
            return $value->value;
        }

        return null;
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

class_alias(Type::class, 'eZ\Publish\Core\FieldType\Null\Type');
