<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Selection;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * The Selection field type.
 *
 * This field type represents a simple string.
 */
class Type extends FieldType
{
    /**
     * The setting keys which are available on this field type.
     *
     * The key is the setting name, and the value is the default value for given
     * setting, set to null if no particular default should be set.
     *
     * @var mixed
     */
    protected $settingsSchema = [
        'isMultiple' => [
            'type' => 'bool',
            'default' => false,
        ],
        'options' => [
            'type' => 'hash',
            'default' => [],
        ],
        'multilingualOptions' => [
            'type' => 'hash',
            'default' => [],
        ],
    ];

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

        foreach ($fieldSettings as $settingKey => $settingValue) {
            switch ($settingKey) {
                case 'isMultiple':
                    if (!is_bool($settingValue)) {
                        $validationErrors[] = new ValidationError(
                            "FieldType '%fieldType%' expects setting '%setting%' to be of type '%type%'",
                            null,
                            [
                                '%fieldType%' => $this->getFieldTypeIdentifier(),
                                '%setting%' => $settingKey,
                                '%type%' => 'bool',
                            ],
                            "[$settingKey]"
                        );
                    }
                    break;
                case 'options':
                    if (!is_array($settingValue)) {
                        $validationErrors[] = new ValidationError(
                            "FieldType '%fieldType%' expects setting '%setting%' to be of type '%type%'",
                            null,
                            [
                                '%fieldType%' => $this->getFieldTypeIdentifier(),
                                '%setting%' => $settingKey,
                                '%type%' => 'hash',
                            ],
                            "[$settingKey]"
                        );
                    }
                    break;
                case 'multilingualOptions':
                    if (!is_array($settingValue) && !is_array(reset($settingValue))) {
                        $validationErrors[] = new ValidationError(
                            "FieldType '%fieldType%' expects setting '%setting%' to be of type '%type%'",
                            null,
                            [
                                '%fieldType%' => $this->getFieldTypeIdentifier(),
                                '%setting%' => $settingKey,
                                '%type%' => 'hash',
                            ],
                            "[$settingKey]"
                        );
                    }
                    break;
                default:
                    $validationErrors[] = new ValidationError(
                        "Setting '%setting%' is unknown",
                        null,
                        [
                            '%setting%' => $settingKey,
                        ],
                        "[$settingKey]"
                    );
            }
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
        return 'ezselection';
    }

    /**
     * @param \Ibexa\Core\FieldType\Selection\Value|\Ibexa\Contracts\Core\FieldType\Value $value
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        if (empty($value->selection)) {
            return '';
        }

        $names = [];
        $fieldSettings = $fieldDefinition->getFieldSettings();

        foreach ($value->selection as $optionIndex) {
            if (isset($fieldSettings['multilingualOptions'][$languageCode][$optionIndex])) {
                $names[] = $fieldSettings['multilingualOptions'][$languageCode][$optionIndex];
            } elseif (isset($fieldSettings['multilingualOptions'][$fieldDefinition->mainLanguageCode][$optionIndex])) {
                $names[] = $fieldSettings['multilingualOptions'][$fieldDefinition->mainLanguageCode][$optionIndex];
            } elseif (isset($fieldSettings['options'][$optionIndex])) {
                $names[] = $fieldSettings['options'][$optionIndex];
            }
        }

        return implode(' ', $names);
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \Ibexa\Core\FieldType\Selection\Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param array|\Ibexa\Core\FieldType\Selection\Value $inputValue
     *
     * @return \Ibexa\Core\FieldType\Selection\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput($inputValue)
    {
        if (is_array($inputValue)) {
            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \Ibexa\Core\FieldType\Selection\Value $value
     */
    protected function checkValueStructure(BaseValue $value)
    {
        if (!is_array($value->selection)) {
            throw new InvalidArgumentType(
                '$value->selection',
                'array',
                $value->selection
            );
        }
    }

    /**
     * Validates field value against 'isMultiple' and 'options' settings.
     *
     * Does not use validators.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition The field definition of the field
     * @param \Ibexa\Core\FieldType\Selection\Value $fieldValue The field value for which an action is performed
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
            && count($fieldValue->selection) > 1) {
            $validationErrors[] = new ValidationError(
                'Field definition does not allow multiple options to be selected.',
                null,
                [],
                'selection'
            );
        }

        foreach ($fieldValue->selection as $optionIndex) {
            if (!isset($fieldSettings['options'][$optionIndex]) && empty($fieldSettings['multilingualOptions'])) {
                $validationErrors[] = new ValidationError(
                    'Option with index %index% does not exist in the field definition.',
                    null,
                    [
                        '%index%' => $optionIndex,
                    ],
                    'selection'
                );
            }
        }

        //@todo: find a way to include selection language
        if (isset($fieldSettings['multilingualOptions'])) {
            $possibleOptionIndexesByLanguage = array_map(static function ($languageOptionIndexes) {
                return array_keys($languageOptionIndexes);
            }, $fieldSettings['multilingualOptions']);

            $possibleOptionIndexes = array_merge(...array_values($possibleOptionIndexesByLanguage));

            foreach ($fieldValue->selection as $optionIndex) {
                if (!in_array($optionIndex, $possibleOptionIndexes)) {
                    $validationErrors[] = new ValidationError(
                        'Option with index %index% does not exist in the field definition.',
                        null,
                        [
                            '%index%' => $optionIndex,
                        ],
                        'selection'
                    );
                }
            }
        }

        return $validationErrors;
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @param \Ibexa\Core\FieldType\Selection\Value $value
     *
     * @return string
     */
    protected function getSortInfo(BaseValue $value)
    {
        return implode('-', $value->selection);
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return \Ibexa\Core\FieldType\Selection\Value $value
     */
    public function fromHash($hash)
    {
        return new Value($hash);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \Ibexa\Core\FieldType\Selection\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        return $value->selection;
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

class_alias(Type::class, 'eZ\Publish\Core\FieldType\Selection\Type');
