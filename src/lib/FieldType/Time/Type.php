<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Time;

use DateTime;
use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as BaseValue;

class Type extends FieldType
{
    /**
     * Default value type empty.
     */
    public const DEFAULT_EMPTY = 0;

    /**
     * Default value type current date.
     */
    public const DEFAULT_CURRENT_TIME = 1;

    protected $settingsSchema = [
        'useSeconds' => [
            'type' => 'bool',
            'default' => false,
        ],
        // One of the DEFAULT_* class constants
        'defaultType' => [
            'type' => 'choice',
            'default' => self::DEFAULT_EMPTY,
        ],
    ];

    /**
     * Returns the field type identifier for this field type.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return 'eztime';
    }

    /**
     * @param \Ibexa\Core\FieldType\Time\Value|\Ibexa\Contracts\Core\FieldType\Value $value
     *
     * @throws \Exception
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        if ($this->isEmptyValue($value)) {
            return '';
        }

        $dateTime = new DateTime("@{$value->time}");

        return $dateTime->format('g:i:s a');
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \Ibexa\Core\FieldType\Time\Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param string|int|\DateTime|\Ibexa\Core\FieldType\Time\Value $inputValue
     *
     * @return \Ibexa\Core\FieldType\Time\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput($inputValue)
    {
        if (is_string($inputValue)) {
            $inputValue = Value::fromString($inputValue);
        }

        if (is_int($inputValue)) {
            $inputValue = Value::fromTimestamp($inputValue);
        }

        if ($inputValue instanceof DateTime) {
            $inputValue = Value::fromDateTime($inputValue);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \Ibexa\Core\FieldType\Time\Value $value
     */
    protected function checkValueStructure(BaseValue $value)
    {
        if (!is_int($value->time)) {
            throw new InvalidArgumentType(
                '$value->time',
                'DateTime',
                $value->time
            );
        }
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @param \Ibexa\Core\FieldType\Time\Value $value
     *
     * @return int
     */
    protected function getSortInfo(BaseValue $value)
    {
        return $value->time;
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param int $hash Number of seconds since Unix Epoch
     *
     * @return \Ibexa\Core\FieldType\Time\Value $value
     */
    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        return new Value($hash);
    }

    /**
     * Returns if the given $value is considered empty by the field type.
     *
     *
     * @param \Ibexa\Core\FieldType\Value $value
     *
     * @return bool
     */
    public function isEmptyValue(SPIValue $value)
    {
        if ($value->time === null) {
            return true;
        }

        return false;
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \Ibexa\Core\FieldType\Time\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        return $value->time;
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

    /**
     * Validates the fieldSettings of a FieldDefinitionCreateStruct or FieldDefinitionUpdateStruct.
     *
     * @param mixed $fieldSettings
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validateFieldSettings($fieldSettings)
    {
        $validationErrors = [];

        foreach ($fieldSettings as $name => $value) {
            if (!isset($this->settingsSchema[$name])) {
                $validationErrors[] = new ValidationError(
                    "Setting '%setting%' is unknown",
                    null,
                    [
                        '%setting%' => $name,
                    ],
                    "[$name]"
                );
                continue;
            }

            switch ($name) {
                case 'useSeconds':
                    if (!is_bool($value)) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be of boolean type",
                            null,
                            [
                                '%setting%' => $name,
                            ],
                            "[$name]"
                        );
                    }
                    break;
                case 'defaultType':
                    $definedTypes = [
                        self::DEFAULT_EMPTY,
                        self::DEFAULT_CURRENT_TIME,
                    ];
                    if (!in_array($value, $definedTypes, true)) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' is of unknown type",
                            null,
                            [
                                '%setting%' => $name,
                            ],
                            "[$name]"
                        );
                    }
                    break;
            }
        }

        return $validationErrors;
    }
}

class_alias(Type::class, 'eZ\Publish\Core\FieldType\Time\Type');
