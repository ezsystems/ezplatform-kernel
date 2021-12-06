<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\BinaryBase;

use Ibexa\Contracts\Core\FieldType\BinaryBase\RouteAwarePathGenerator;
use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue as PersistenceValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\Media\Value;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Base FileType class for Binary field types (i.e. BinaryBase & Media).
 */
abstract class Type extends FieldType
{
    protected $validatorConfigurationSchema = [
        'FileSizeValidator' => [
            'maxFileSize' => [
                'type' => 'int',
                'default' => null,
            ],
        ],
    ];

    /** @var \Ibexa\Core\FieldType\Validator[] */
    private $validators;

    /** @var \Ibexa\Contracts\Core\FieldType\BinaryBase\RouteAwarePathGenerator|null */
    protected $routeAwarePathGenerator;

    /**
     * @param \Ibexa\Core\FieldType\Validator[] $validators
     */
    public function __construct(array $validators, ?RouteAwarePathGenerator $routeAwarePathGenerator = null)
    {
        $this->validators = $validators;
        $this->routeAwarePathGenerator = $routeAwarePathGenerator;
    }

    /**
     * Creates a specific value of the derived class from $inputValue.
     *
     * @param array $inputValue
     *
     * @return \Ibexa\Core\FieldType\Media\Value
     */
    abstract protected function createValue(array $inputValue);

    final protected function regenerateUri(array $inputValue): array
    {
        if (isset($this->routeAwarePathGenerator, $inputValue['route'])) {
            $inputValue['uri'] = $this->routeAwarePathGenerator->generate(
                $inputValue['route'],
                $inputValue['route_parameters'] ?? []
            );
        }

        unset($inputValue['route'], $inputValue['route_parameters']);

        return $inputValue;
    }

    /**
     * @param \Ibexa\Core\FieldType\BinaryBase\Value|\Ibexa\Contracts\Core\FieldType\Value $value
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        return (string)$value->fileName;
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param string|array|\Ibexa\Core\FieldType\BinaryBase\Value $inputValue
     *
     * @return \Ibexa\Core\FieldType\BinaryBase\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput($inputValue)
    {
        // construction only from path
        if (is_string($inputValue)) {
            $inputValue = ['inputUri' => $inputValue];
        }

        // default construction from array
        if (is_array($inputValue)) {
            $inputValue = $this->createValue($inputValue);
        }

        $this->completeValue($inputValue);

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \Ibexa\Core\FieldType\BinaryBase\Value $value
     */
    protected function checkValueStructure(BaseValue $value)
    {
        // Input file URI, if set needs to point to existing file
        if (isset($value->inputUri)) {
            if (!file_exists($value->inputUri)) {
                throw new InvalidArgumentValue(
                    '$value->inputUri',
                    $value->inputUri,
                    static::class
                );
            }
        } elseif (!isset($value->id)) {
            throw new InvalidArgumentValue(
                '$value->id',
                $value->id,
                static::class
            );
        }

        // Required parameter $fileName
        if (!isset($value->fileName) || !is_string($value->fileName)) {
            throw new InvalidArgumentValue(
                '$value->fileName',
                $value->fileName,
                static::class
            );
        }

        // Optional parameter $fileSize
        if (isset($value->fileSize) && !is_int($value->fileSize)) {
            throw new InvalidArgumentValue(
                '$value->fileSize',
                $value->fileSize,
                static::class
            );
        }
    }

    /**
     * Attempts to complete the data in $value.
     *
     * @param \Ibexa\Core\FieldType\BinaryBase\Value|\Ibexa\Core\FieldType\Value $value
     */
    protected function completeValue(BaseValue $value)
    {
        if (!isset($value->inputUri) || !file_exists($value->inputUri)) {
            return;
        }

        if (!isset($value->fileName)) {
            // @todo this may not always work...
            $value->fileName = basename($value->inputUri);
        }

        if (!isset($value->fileSize)) {
            $value->fileSize = filesize($value->inputUri);
        }
    }

    /**
     * BinaryBase does not support sorting, yet.
     *
     * @param \Ibexa\Core\FieldType\BinaryBase\Value $value
     *
     * @return mixed
     */
    protected function getSortInfo(BaseValue $value)
    {
        return false;
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return \Ibexa\Core\FieldType\BinaryBase\Value $value
     */
    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        return $this->createValue($hash);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \Ibexa\Core\FieldType\BinaryBase\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        return [
            'id' => $value->id,
            // Kept for BC with eZ Publish 5.0 (EZP-20948, EZP-22808)
            'path' => $value->inputUri,
            'inputUri' => $value->inputUri,
            'fileName' => $value->fileName,
            'fileSize' => $value->fileSize,
            'mimeType' => $value->mimeType,
            'uri' => $value->uri,
        ];
    }

    public function toPersistenceValue(SPIValue $value)
    {
        // Store original data as external (to indicate they need to be stored)
        return new PersistenceValue(
            [
                'data' => null,
                'externalData' => $this->toHash($value),
                'sortKey' => $this->getSortInfo($value),
            ]
        );
    }

    /**
     * Converts a persistence $fieldValue to a Value.
     *
     * This method builds a field type value from the $data and $externalData properties.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\FieldValue $fieldValue
     *
     * @return \Ibexa\Core\FieldType\BinaryBase\Value
     */
    public function fromPersistenceValue(PersistenceValue $fieldValue)
    {
        // Restored data comes in $data, since it has already been processed
        // there might be more data in the persistence value than needed here
        $hash = [
            'id' => $fieldValue->externalData['id'] ?? null,
            'fileName' => $fieldValue->externalData['fileName'] ?? null,
            'fileSize' => $fieldValue->externalData['fileSize'] ?? null,
            'mimeType' => $fieldValue->externalData['mimeType'] ?? null,
            'uri' => $fieldValue->externalData['uri'] ?? null,
        ];

        if (isset($fieldValue->externalData['route'])) {
            $hash['route'] = $fieldValue->externalData['route'];
        }

        if (isset($fieldValue->externalData['route_parameters'])) {
            $hash['route_parameters'] = $fieldValue->externalData['route_parameters'];
        }

        return $this->fromHash($hash);
    }

    /**
     * Validates a field based on the validators in the field definition.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition The field definition of the field
     * @param \Ibexa\Core\FieldType\BinaryBase\Value $fieldValue The field value for which an action is performed
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue)
    {
        $errors = [];

        if ($this->isEmptyValue($fieldValue)) {
            return $errors;
        }

        foreach ($this->validators as $externalValidator) {
            if (!$externalValidator->validate($fieldValue)) {
                $errors = array_merge($errors, $externalValidator->getMessage());
            }
        }

        foreach ((array)$fieldDefinition->getValidatorConfiguration() as $validatorIdentifier => $parameters) {
            switch ($validatorIdentifier) {
                // @todo There is a risk if we rely on a user built Value, since the FileSize
                // property can be set manually, making this validation pointless
                case 'FileSizeValidator':
                    if (empty($parameters['maxFileSize'])) {
                        // No file size limit
                        break;
                    }
                    // Database stores maxFileSize in MB
                    if (($parameters['maxFileSize'] * 1024 * 1024) < $fieldValue->fileSize) {
                        $errors[] = new ValidationError(
                            'The file size cannot exceed %size% byte.',
                            'The file size cannot exceed %size% bytes.',
                            [
                                '%size%' => $parameters['maxFileSize'],
                            ],
                            'fileSize'
                        );
                    }
                    break;
            }
        }

        return $errors;
    }

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

        foreach ($validatorConfiguration as $validatorIdentifier => $parameters) {
            switch ($validatorIdentifier) {
                case 'FileSizeValidator':
                    if (!array_key_exists('maxFileSize', $parameters)) {
                        $validationErrors[] = new ValidationError(
                            'Validator %validator% expects parameter %parameter% to be set.',
                            null,
                            [
                                '%validator%' => $validatorIdentifier,
                                '%parameter%' => 'maxFileSize',
                            ],
                            "[$validatorIdentifier][maxFileSize]"
                        );
                        break;
                    }
                    if (!is_int($parameters['maxFileSize']) && $parameters['maxFileSize'] !== null) {
                        $validationErrors[] = new ValidationError(
                            'Validator %validator% expects parameter %parameter% to be of %type%.',
                            null,
                            [
                                '%validator%' => $validatorIdentifier,
                                '%parameter%' => 'maxFileSize',
                                '%type%' => 'integer',
                                "[$validatorIdentifier][maxFileSize]",
                            ]
                        );
                    }
                    break;
                default:
                    $validationErrors[] = new ValidationError(
                        "Validator '%validator%' is unknown",
                        null,
                        [
                            '%validator%' => $validatorIdentifier,
                        ],
                        "[$validatorIdentifier]"
                    );
            }
        }

        return $validationErrors;
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

class_alias(Type::class, 'eZ\Publish\Core\FieldType\BinaryBase\Type');
