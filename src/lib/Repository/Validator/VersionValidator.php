<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Validator;

use Ibexa\Contracts\Core\Repository\Validator\ContentValidator;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\FieldType\FieldTypeRegistry;
use Ibexa\Core\FieldType\ValidationError;

/**
 * @internal Meant for internal use by Repository
 */
final class VersionValidator implements ContentValidator
{
    /** @var \Ibexa\Core\FieldType\FieldTypeRegistry */
    private $fieldTypeRegistry;

    public function __construct(
        FieldTypeRegistry $fieldTypeRegistry
    ) {
        $this->fieldTypeRegistry = $fieldTypeRegistry;
    }

    public function supports(ValueObject $object): bool
    {
        return $object instanceof VersionInfo;
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
            throw new InvalidArgumentException('$context', sprintf(
                'Context "content" parameter is not an instance of %s',
                Content::class
            ));
        }

        $content = $context['content'];

        $contentType = $content->getContentType();
        $languageCodes = $content->versionInfo->languageCodes;

        $allFieldErrors = [];

        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            if (isset($fieldIdentifiers) && !in_array($fieldDefinition->fieldTypeIdentifier, $fieldIdentifiers)) {
                continue;
            }

            $fieldType = $this->fieldTypeRegistry->getFieldType(
                $fieldDefinition->fieldTypeIdentifier
            );

            foreach ($languageCodes as $languageCode) {
                $fieldValue = $content->getField($fieldDefinition->identifier)->value ?? $fieldDefinition->defaultValue;
                $fieldValue = $fieldType->acceptValue($fieldValue);

                if ($fieldType->isEmptyValue($fieldValue)) {
                    if ($fieldDefinition->isRequired) {
                        $allFieldErrors[$fieldDefinition->identifier][$languageCode] = new ValidationError(
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
                        $allFieldErrors[$fieldDefinition->identifier][$languageCode] = $fieldErrors;
                    }
                }
            }
        }

        return $allFieldErrors;
    }
}

class_alias(VersionValidator::class, 'eZ\Publish\Core\Repository\Validator\VersionValidator');
