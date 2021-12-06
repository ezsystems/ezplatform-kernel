<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Validator;

use Ibexa\Contracts\Core\Persistence\Content\Language\Handler;
use Ibexa\Contracts\Core\Repository\Validator\ContentValidator;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\FieldType\FieldTypeRegistry;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\Repository\Mapper\ContentMapper;

/**
 * @internal Meant for internal use by Repository
 */
final class ContentUpdateStructValidator implements ContentValidator
{
    /** @var \Ibexa\Core\Repository\Mapper\ContentMapper */
    private $contentMapper;

    /** @var \Ibexa\Core\FieldType\FieldTypeRegistry */
    private $fieldTypeRegistry;

    /** @var \Ibexa\Core\Persistence\Legacy\Content\Language\Handler */
    private $contentLanguageHandler;

    public function __construct(
        ContentMapper $contentMapper,
        FieldTypeRegistry $fieldTypeRegistry,
        Handler $contentLanguageHandler
    ) {
        $this->contentMapper = $contentMapper;
        $this->contentLanguageHandler = $contentLanguageHandler;
        $this->fieldTypeRegistry = $fieldTypeRegistry;
    }

    public function supports(ValueObject $object): bool
    {
        return $object instanceof ContentUpdateStruct;
    }

    public function validate(
        ValueObject $object,
        array $context = [],
        ?array $fieldIdentifiers = null
    ): array {
        if (!$this->supports($object)) {
            throw new InvalidArgumentException('$object', 'Not supported');
        }

        if (empty($context['content']) || !$context['content'] instanceof Content) {
            throw new InvalidArgumentException('context[content]', 'Must be a ' . Content::class . ' type');
        }

        $content = $context['content'];

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct $contentUpdateStruct */
        $contentUpdateStruct = $object;
        $contentType = $content->getContentType();

        $mainLanguageCode = $content->contentInfo->mainLanguageCode;
        if ($contentUpdateStruct->initialLanguageCode === null) {
            $contentUpdateStruct->initialLanguageCode = $mainLanguageCode;
        }

        $allLanguageCodes = $this->contentMapper->getLanguageCodesForUpdate($contentUpdateStruct, $content);
        foreach ($allLanguageCodes as $languageCode) {
            $this->contentLanguageHandler->loadByLanguageCode($languageCode);
        }

        $updatedLanguageCodes = $this->contentMapper->getUpdatedLanguageCodes($contentUpdateStruct);
        $fields = $this->contentMapper->mapFieldsForUpdate(
            $contentUpdateStruct,
            $contentType,
            $mainLanguageCode
        );

        $allFieldErrors = [];

        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            if (isset($fieldIdentifiers) && !in_array($fieldDefinition->fieldTypeIdentifier, $fieldIdentifiers)) {
                continue;
            }

            $fieldType = $this->fieldTypeRegistry->getFieldType(
                $fieldDefinition->fieldTypeIdentifier
            );

            foreach ($allLanguageCodes as $languageCode) {
                $isLanguageNew = !in_array($languageCode, $content->versionInfo->languageCodes);
                $isLanguageUpdated = in_array($languageCode, $updatedLanguageCodes);
                $valueLanguageCode = $fieldDefinition->isTranslatable ? $languageCode : $mainLanguageCode;
                $isFieldUpdated = isset($fields[$fieldDefinition->identifier][$valueLanguageCode]);

                if (!$isFieldUpdated && !$isLanguageNew) {
                    $fieldValue = $content->getField($fieldDefinition->identifier, $valueLanguageCode)->value;
                } elseif (!$isFieldUpdated && $isLanguageNew && !$fieldDefinition->isTranslatable) {
                    $fieldValue = $content->getField($fieldDefinition->identifier, $valueLanguageCode)->value;
                } elseif ($isFieldUpdated) {
                    $fieldValue = $fields[$fieldDefinition->identifier][$valueLanguageCode]->value;
                } else {
                    $fieldValue = $fieldDefinition->defaultValue;
                }

                $fieldValue = $fieldType->acceptValue($fieldValue);

                if ($fieldType->isEmptyValue($fieldValue)) {
                    if ($isLanguageUpdated && $fieldDefinition->isRequired) {
                        $allFieldErrors[$fieldDefinition->id][$languageCode] = new ValidationError(
                            "Value for required field definition '%identifier%' with language '%languageCode%' is empty",
                            null,
                            ['%identifier%' => $fieldDefinition->identifier, '%languageCode%' => $languageCode],
                            'empty'
                        );
                    }
                } elseif ($isLanguageUpdated) {
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

class_alias(ContentUpdateStructValidator::class, 'eZ\Publish\Core\Repository\Validator\ContentUpdateStructValidator');
