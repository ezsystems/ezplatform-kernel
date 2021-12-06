<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Country;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\Country\Exception\InvalidValue;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * The Country field type.
 *
 * This field type represents a simple string.
 */
class Type extends FieldType
{
    protected $settingsSchema = [
        'isMultiple' => [
            'type' => 'boolean',
            'default' => false,
        ],
    ];

    /** @var array */
    protected $countriesInfo;

    /**
     * @param array $countriesInfo Array of countries data
     */
    public function __construct(array $countriesInfo)
    {
        $this->countriesInfo = $countriesInfo;
    }

    /**
     * Returns the field type identifier for this field type.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return 'ezcountry';
    }

    /**
     * @param \Ibexa\Core\FieldType\Country\Value|\Ibexa\Contracts\Core\FieldType\Value $value
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        return (string)$value;
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \Ibexa\Core\FieldType\Country\Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param array|\Ibexa\Core\FieldType\Country\Value $inputValue
     *
     * @return \Ibexa\Core\FieldType\Country\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput($inputValue)
    {
        if (is_array($inputValue)) {
            $inputValue = $this->fromHash($inputValue);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \Ibexa\Core\FieldType\Country\Value $value
     */
    protected function checkValueStructure(BaseValue $value)
    {
        if (!is_array($value->countries)) {
            throw new InvalidArgumentType(
                '$value->countries',
                'array',
                $value->countries
            );
        }
    }

    /**
     * Validates field value against 'isMultiple' setting.
     *
     * Does not use validators.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition The field definition of the field
     * @param \Ibexa\Core\FieldType\Country\Value $fieldValue The field value for which an action is performed
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue)
    {
        $validationErrors = [];

        if ($this->isEmptyValue($fieldValue)) {
            return $validationErrors;
        }

        $fieldSettings = $fieldDefinition->getFieldSettings();

        if ((!isset($fieldSettings['isMultiple']) || $fieldSettings['isMultiple'] === false)
            && count($fieldValue->countries) > 1) {
            $validationErrors[] = new ValidationError(
                'Field definition does not allow multiple countries to be selected.',
                null,
                [],
                'countries'
            );
        }

        foreach ($fieldValue->countries as $alpha2 => $countryInfo) {
            if (!isset($this->countriesInfo[$alpha2])) {
                $validationErrors[] = new ValidationError(
                    "Country with Alpha2 code '%alpha2%' is not defined in FieldType settings.",
                    null,
                    [
                        '%alpha2%' => $alpha2,
                    ],
                    'countries'
                );
            }
        }

        return $validationErrors;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSortInfo(BaseValue $value)
    {
        $countries = [];
        foreach ($value->countries as $countryInfo) {
            $countries[] = $this->transformationProcessor->transformByGroup($countryInfo['Name'], 'lowercase');
        }

        sort($countries);

        return implode(',', $countries);
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return \Ibexa\Core\FieldType\Country\Value $value
     */
    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        $countries = [];
        foreach ($hash as $country) {
            foreach ($this->countriesInfo as $countryInfo) {
                switch ($country) {
                    case $countryInfo['Name']:
                    case $countryInfo['Alpha2']:
                    case $countryInfo['Alpha3']:
                        $countries[$countryInfo['Alpha2']] = $countryInfo;
                        continue 3;
                }
            }

            throw new InvalidValue($country);
        }

        return new Value($countries);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \Ibexa\Core\FieldType\Country\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        return array_keys($value->countries);
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
                case 'isMultiple':
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
            }
        }

        return $validationErrors;
    }
}

class_alias(Type::class, 'eZ\Publish\Core\FieldType\Country\Type');
