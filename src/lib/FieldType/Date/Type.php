<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Date;

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
    public const DEFAULT_CURRENT_DATE = 1;

    protected $settingsSchema = [
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
        return 'ezdate';
    }

    /**
     * @param \Ibexa\Core\FieldType\Date\Value|\Ibexa\Contracts\Core\FieldType\Value $value
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        if ($this->isEmptyValue($value)) {
            return '';
        }

        return $value->date->format('l d F Y');
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \Ibexa\Core\FieldType\Date\Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param string|int|\DateTime|\Ibexa\Core\FieldType\Date\Value $inputValue
     *
     * @return \Ibexa\Core\FieldType\Date\Value The potentially converted and structurally plausible value.
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
            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \Ibexa\Core\FieldType\Date\Value $value
     */
    protected function checkValueStructure(BaseValue $value)
    {
        if (!$value->date instanceof DateTime) {
            throw new InvalidArgumentType(
                (string)$value->date,
                'DateTime',
                $value->date
            );
        }
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @param \Ibexa\Core\FieldType\Date\Value $value
     *
     * @return mixed
     */
    protected function getSortInfo(BaseValue $value)
    {
        if ($value->date === null) {
            return null;
        }

        return $value->date->getTimestamp();
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash Null or associative array containing one of the following (first value found in the order below is picked):
     *                    'rfc850': Date in RFC 850 format (DateTime::RFC850)
     *                    'timestring': Date in parseable string format supported by DateTime (e.g. 'now', '+3 days')
     *                    'timestamp': Unix timestamp
     *
     * @return \Ibexa\Core\FieldType\Date\Value $value
     */
    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        if (isset($hash['rfc850']) && $hash['rfc850']) {
            return Value::fromString($hash['rfc850']);
        }

        if (isset($hash['timestring']) && is_string($hash['timestring'])) {
            return Value::fromString($hash['timestring']);
        }

        return Value::fromTimestamp((int)$hash['timestamp']);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \Ibexa\Core\FieldType\Date\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        if ($value->date instanceof DateTime) {
            return [
                'timestamp' => $value->date->getTimestamp(),
                'rfc850' => $value->date->format(DateTime::RFC850),
            ];
        }

        return [
            'timestamp' => 0,
            'rfc850' => null,
        ];
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
                case 'defaultType':
                    $definedTypes = [
                        self::DEFAULT_EMPTY,
                        self::DEFAULT_CURRENT_DATE,
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

class_alias(Type::class, 'eZ\Publish\Core\FieldType\Date\Type');
