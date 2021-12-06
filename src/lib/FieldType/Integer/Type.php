<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Integer;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Integer field types.
 *
 * Represents integers.
 */
class Type extends FieldType
{
    protected $validatorConfigurationSchema = [
        'IntegerValueValidator' => [
            'minIntegerValue' => [
                'type' => 'int',
                'default' => null,
            ],
            'maxIntegerValue' => [
                'type' => 'int',
                'default' => null,
            ],
        ],
    ];

    /**
     * Validates the validatorConfiguration of a FieldDefinitionCreateStruct or FieldDefinitionUpdateStruct.
     *
     * @param mixed $validatorConfiguration
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validateValidatorConfiguration($validatorConfiguration)
    {
        $validationErrors = [];

        foreach ($validatorConfiguration as $validatorIdentifier => $constraints) {
            if ($validatorIdentifier !== 'IntegerValueValidator') {
                $validationErrors[] = new ValidationError(
                    "Validator '%validator%' is unknown",
                    null,
                    [
                        '%validator%' => $validatorIdentifier,
                    ],
                    "[$validatorIdentifier]"
                );

                continue;
            }
            foreach ($constraints as $name => $value) {
                switch ($name) {
                    case 'minIntegerValue':
                    case 'maxIntegerValue':
                        if ($value !== null && !is_int($value)) {
                            $validationErrors[] = new ValidationError(
                                "Validator parameter '%parameter%' value must be of integer type",
                                null,
                                [
                                    '%parameter%' => $name,
                                ],
                                "[$validatorIdentifier][$name]"
                            );
                        }
                        break;
                    default:
                        $validationErrors[] = new ValidationError(
                            "Validator parameter '%parameter%' is unknown",
                            null,
                            [
                                '%parameter%' => $name,
                            ],
                            "[$validatorIdentifier][$name]"
                        );
                }
            }
        }

        return $validationErrors;
    }

    /**
     * Validates a field based on the validators in the field definition.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition The field definition of the field
     * @param \Ibexa\Core\FieldType\Integer\Value $fieldValue The field value for which an action is performed
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue)
    {
        $validationErrors = [];

        if ($this->isEmptyValue($fieldValue)) {
            return $validationErrors;
        }

        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
        $constraints = isset($validatorConfiguration['IntegerValueValidator']) ?
            $validatorConfiguration['IntegerValueValidator'] :
            [];

        $validationErrors = [];

        // 0 and False are unlimited value for maxIntegerValue
        if (isset($constraints['maxIntegerValue']) &&
            $constraints['maxIntegerValue'] !== 0 &&
            $constraints['maxIntegerValue'] !== false &&
            $fieldValue->value > $constraints['maxIntegerValue']
        ) {
            $validationErrors[] = new ValidationError(
                'The value can not be higher than %size%.',
                null,
                [
                    '%size%' => $constraints['maxIntegerValue'],
                ],
                'value'
            );
        }

        if (isset($constraints['minIntegerValue']) &&
            $constraints['minIntegerValue'] !== false && $fieldValue->value < $constraints['minIntegerValue']) {
            $validationErrors[] = new ValidationError(
                'The value can not be lower than %size%.',
                null,
                [
                    '%size%' => $constraints['minIntegerValue'],
                ],
                'value'
            );
        }

        return $validationErrors;
    }

    /**
     * Returns the field type identifier for this field type.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return 'ezinteger';
    }

    /**
     * @param \Ibexa\Core\FieldType\Integer\Value|\Ibexa\Contracts\Core\FieldType\Value $value
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        return (string)$value;
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \Ibexa\Core\FieldType\Integer\Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Returns if the given $value is considered empty by the field type.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isEmptyValue(SPIValue $value)
    {
        return $value->value === null;
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param int|\Ibexa\Core\FieldType\Integer\Value $inputValue
     *
     * @return \Ibexa\Core\FieldType\Integer\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput($inputValue)
    {
        if (is_int($inputValue)) {
            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \Ibexa\Core\FieldType\Integer\Value $value
     */
    protected function checkValueStructure(BaseValue $value)
    {
        if (!is_int($value->value)) {
            throw new InvalidArgumentType(
                '$value->value',
                'integer',
                $value->value
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getSortInfo(BaseValue $value)
    {
        return $value->value;
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return \Ibexa\Core\FieldType\Integer\Value $value
     */
    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        return new Value($hash);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \Ibexa\Core\FieldType\Integer\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        return $value->value;
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

class_alias(Type::class, 'eZ\Publish\Core\FieldType\Integer\Type');
