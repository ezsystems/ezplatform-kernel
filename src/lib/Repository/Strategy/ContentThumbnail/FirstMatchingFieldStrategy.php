<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Strategy\ContentThumbnail;

use Ibexa\Contracts\Core\Repository\FieldTypeService;
use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\Field\ThumbnailStrategy as ContentFieldThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

final class FirstMatchingFieldStrategy implements ThumbnailStrategy
{
    /** @var \Ibexa\Contracts\Core\Repository\FieldTypeService */
    private $fieldTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\Field\ThumbnailStrategy */
    private $contentFieldStrategy;

    public function __construct(
        ContentFieldThumbnailStrategy $contentFieldStrategy,
        FieldTypeService $fieldTypeService
    ) {
        $this->contentFieldStrategy = $contentFieldStrategy;
        $this->fieldTypeService = $fieldTypeService;
    }

    public function getThumbnail(ContentType $contentType, array $fields, ?VersionInfo $versionInfo = null): ?Thumbnail
    {
        $fieldDefinitions = $contentType->getFieldDefinitions();

        foreach ($fieldDefinitions as $fieldDefinition) {
            $field = $this->getFieldByIdentifier($fieldDefinition->identifier, $fields);

            if ($field === null) {
                continue;
            }

            $fieldType = $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);

            if (
                $fieldDefinition->isThumbnail
                && $this->contentFieldStrategy->hasStrategy($field->fieldTypeIdentifier)
                && !$fieldType->isEmptyValue($field->value)
            ) {
                return $this->contentFieldStrategy->getThumbnail($field, $versionInfo);
            }
        }

        return null;
    }

    private function getFieldByIdentifier(string $identifier, array $fields): ?Field
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field $field */
        foreach ($fields as $field) {
            if ($field->fieldDefIdentifier === $identifier) {
                return $field;
            }
        }

        return null;
    }
}

class_alias(FirstMatchingFieldStrategy::class, 'eZ\Publish\Core\Repository\Strategy\ContentThumbnail\FirstMatchingFieldStrategy');
