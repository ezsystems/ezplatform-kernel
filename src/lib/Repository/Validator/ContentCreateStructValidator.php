<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Validator;

use Ibexa\Contracts\Core\Repository\Validator\ContentValidator;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\FieldType\FieldTypeRegistry;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\Repository\Mapper\ContentMapper;

/**
 * @internal Meant for internal use by Repository
 */
final class ContentCreateStructValidator implements ContentValidator
{
    /** @var \Ibexa\Core\Repository\Mapper\ContentMapper */
    private $contentMapper;

    /** @var \Ibexa\Core\FieldType\FieldTypeRegistry */
    private $fieldTypeRegistry;

    public function __construct(
        ContentMapper $contentMapper,
        FieldTypeRegistry $fieldTypeRegistry
    ) {
        $this->contentMapper = $contentMapper;
        $this->fieldTypeRegistry = $fieldTypeRegistry;
    }

    public function supports(ValueObject $object): bool
    {
        return $object instanceof ContentCreateStruct;
    }

    public function validate(
        ValueObject $object,
        array $context = [],
        ?array $fieldIdentifiers = null
    ): array {
        if (!$this->supports($object)) {
            throw new InvalidArgumentException('$object', 'Not supported');
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct $contentCreateStruct */
        $contentCreateStruct = $object;

        $languageCodes = $this->contentMapper->getLanguageCodesForCreate($contentCreateStruct);
        $fields = $this->contentMapper->mapFieldsForCreate($contentCreateStruct);

        $allFieldErrors = [];

        foreach ($contentCreateStruct->contentType->getFieldDefinitions() as $fieldDefinition) {
            if (isset($fieldIdentifiers) && !in_array($fieldDefinition->fieldTypeIdentifier, $fieldIdentifiers)) {
                continue;
            }

            /** @var \Ibexa\Core\FieldType\FieldType $fieldType */
            $fieldType = $this->fieldTypeRegistry->getFieldType(
                $fieldDefinition->fieldTypeIdentifier
            );

            foreach ($languageCodes as $languageCode) {
                $valueLanguageCode = $fieldDefinition->isTranslatable ? $languageCode : $contentCreateStruct->mainLanguageCode;
                $fieldValue = isset($fields[$fieldDefinition->identifier][$valueLanguageCode])
                    ? $fields[$fieldDefinition->identifier][$valueLanguageCode]->value
                    : $fieldDefinition->defaultValue;

                $fieldValue = $fieldType->acceptValue($fieldValue);

                if ($fieldType->isEmptyValue($fieldValue)) {
                    if ($fieldDefinition->isRequired) {
                        $allFieldErrors[$fieldDefinition->id][$languageCode] = new ValidationError(
                            "Value for required field definition '%identifier%' with language '%languageCode%' is empty",
                            null,
                            ['%identifier%' => $fieldDefinition->identifier, '%languageCode%' => $languageCode],
                            'empty'
                        );
                    }
                } else {
                    $fieldErrors = $fieldType->validate(
                        $fieldDefinition,
                        $fieldValue
                    );
                    if (!empty($fieldErrors)) {
                        $allFieldErrors[$fieldDefinition->id][$languageCode] = $fieldErrors;
                    }
                }
            }
        }

        return $allFieldErrors;
    }
}

class_alias(ContentCreateStructValidator::class, 'eZ\Publish\Core\Repository\Validator\ContentCreateStructValidator');
