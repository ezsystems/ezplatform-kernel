<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\BinaryFile;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Core\FieldType\BinaryBase\Type as BinaryBaseType;
use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * The TextLine field type.
 *
 * This field type represents a simple string.
 */
class Type extends BinaryBaseType
{
    /**
     * Returns the field type identifier for this field type.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return 'ezbinaryfile';
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \Ibexa\Core\FieldType\BinaryFile\Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Creates a specific value of the derived class from $inputValue.
     *
     * @param array $inputValue
     *
     * @return \Ibexa\Core\FieldType\BinaryFile\Value
     */
    protected function createValue(array $inputValue)
    {
        $inputValue = $this->regenerateUri($inputValue);

        return new Value($inputValue);
    }

    /**
     * Attempts to complete the data in $value.
     *
     * @param \Ibexa\Core\FieldType\BinaryFile\Value|\Ibexa\Core\FieldType\Value $value
     */
    protected function completeValue(Basevalue $value)
    {
        parent::completeValue($value);

        if (isset($value->downloadCount) && $value->downloadCount === null) {
            $value->downloadCount = 0;
        }
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \Ibexa\Core\FieldType\BinaryFile\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        $hash = parent::toHash($value);

        $hash['downloadCount'] = $value->downloadCount;

        return $hash;
    }

    /**
     * Converts a persistence $fieldValue to a Value.
     *
     * This method builds a field type value from the $data and $externalData properties.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\FieldValue $fieldValue
     *
     * @return \Ibexa\Core\FieldType\BinaryFile\Value
     */
    public function fromPersistenceValue(FieldValue $fieldValue)
    {
        if ($fieldValue->externalData === null) {
            return $this->getEmptyValue();
        }

        $result = parent::fromPersistenceValue($fieldValue);

        $result->downloadCount = (isset($fieldValue->externalData['downloadCount'])
            ? $fieldValue->externalData['downloadCount']
            : 0);

        return $result;
    }
}

class_alias(Type::class, 'eZ\Publish\Core\FieldType\BinaryFile\Type');
