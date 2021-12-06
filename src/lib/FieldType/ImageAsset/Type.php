<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\FieldType\ImageAsset;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\Handler as SPIContentHandler;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Relation;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as BaseValue;

class Type extends FieldType
{
    public const FIELD_TYPE_IDENTIFIER = 'ezimageasset';

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Core\FieldType\ImageAsset\AssetMapper */
    private $assetMapper;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Handler */
    private $handler;

    /**
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     * @param \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService
     * @param \Ibexa\Core\FieldType\ImageAsset\AssetMapper $mapper
     * @param \Ibexa\Contracts\Core\Persistence\Content\Handler $handler
     */
    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        AssetMapper $mapper,
        SPIContentHandler $handler
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->assetMapper = $mapper;
        $this->handler = $handler;
    }

    /**
     * Validates a field based on the validators in the field definition.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition The field definition of the field
     * @param \Ibexa\Core\FieldType\ImageAsset\Value $fieldValue The field value for which an action is performed
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue): array
    {
        $errors = [];

        if ($this->isEmptyValue($fieldValue)) {
            return $errors;
        }

        $content = $this->contentService->loadContent(
            (int)$fieldValue->destinationContentId
        );

        if (!$this->assetMapper->isAsset($content)) {
            $currentContentType = $this->contentTypeService->loadContentType(
                (int)$content->contentInfo->contentTypeId
            );

            $errors[] = new ValidationError(
                'Content %type% is not a valid asset target',
                null,
                [
                    '%type%' => $currentContentType->identifier,
                ],
                'destinationContentId'
            );
        }

        return $errors;
    }

    /**
     * Returns the field type identifier for this field type.
     *
     * @return string
     */
    public function getFieldTypeIdentifier(): string
    {
        return self::FIELD_TYPE_IDENTIFIER;
    }

    /**
     * @param \Ibexa\Core\FieldType\ImageAsset\Value|\Ibexa\Contracts\Core\FieldType\Value $value
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        if (empty($value->destinationContentId)) {
            return '';
        }

        try {
            $contentInfo = $this->handler->loadContentInfo($value->destinationContentId);
            $versionInfo = $this->handler->loadVersionInfo($value->destinationContentId, $contentInfo->currentVersionNo);
        } catch (NotFoundException $e) {
            return '';
        }

        return $versionInfo->names[$languageCode] ?? $versionInfo->names[$contentInfo->mainLanguageCode];
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \Ibexa\Core\FieldType\ImageAsset\Value
     */
    public function getEmptyValue(): Value
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
    public function isEmptyValue(SPIValue $value): bool
    {
        return null === $value->destinationContentId;
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param int|string|\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo|\Ibexa\Core\FieldType\Relation\Value $inputValue
     *
     * @return \Ibexa\Core\FieldType\ImageAsset\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput($inputValue)
    {
        if ($inputValue instanceof ContentInfo) {
            $inputValue = new Value($inputValue->id);
        } elseif (is_int($inputValue) || is_string($inputValue)) {
            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \Ibexa\Core\FieldType\ImageAsset\Value $value
     */
    protected function checkValueStructure(BaseValue $value): void
    {
        if (!is_int($value->destinationContentId) && !is_string($value->destinationContentId)) {
            throw new InvalidArgumentType(
                '$value->destinationContentId',
                'string|int',
                $value->destinationContentId
            );
        }

        if ($value->alternativeText !== null && !is_string($value->alternativeText)) {
            throw new InvalidArgumentType(
                '$value->alternativeText',
                'string|null',
                $value->alternativeText
            );
        }
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     * For this FieldType, the related object's name is returned.
     *
     * @param \Ibexa\Core\FieldType\Relation\Value $value
     *
     * @return bool
     */
    protected function getSortInfo(BaseValue $value): bool
    {
        return false;
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return \Ibexa\Core\FieldType\ImageAsset\Value $value
     */
    public function fromHash($hash): Value
    {
        if (!$hash) {
            return new Value();
        }

        $destinationContentId = $hash['destinationContentId'];
        if ($destinationContentId !== null) {
            $destinationContentId = (int)$destinationContentId;
        }

        return new Value($destinationContentId, $hash['alternativeText']);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \Ibexa\Core\FieldType\ImageAsset\Value $value
     *
     * @return array
     */
    public function toHash(SPIValue $value): array
    {
        $destinationContentId = null;
        if ($value->destinationContentId !== null) {
            $destinationContentId = (int)$value->destinationContentId;
        }

        return [
            'destinationContentId' => $destinationContentId,
            'alternativeText' => $value->alternativeText,
        ];
    }

    /**
     * Returns relation data extracted from value.
     *
     * Not intended for \Ibexa\Contracts\Core\Repository\Values\Content\Relation::COMMON type relations,
     * there is an API for handling those.
     *
     * @param \Ibexa\Core\FieldType\ImageAsset\Value $fieldValue
     *
     * @return array Hash with relation type as key and array of destination content ids as value.
     *
     * Example:
     * <code>
     *  array(
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Relation::LINK => array(
     *          "contentIds" => array( 12, 13, 14 ),
     *          "locationIds" => array( 24 )
     *      ),
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Relation::EMBED => array(
     *          "contentIds" => array( 12 ),
     *          "locationIds" => array( 24, 45 )
     *      ),
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Relation::FIELD => array( 12 )
     *  )
     * </code>
     */
    public function getRelations(SPIValue $fieldValue): array
    {
        $relations = [];
        if ($fieldValue->destinationContentId !== null) {
            $relations[Relation::ASSET] = [$fieldValue->destinationContentId];
        }

        return $relations;
    }

    /**
     * Returns whether the field type is searchable.
     *
     * @return bool
     */
    public function isSearchable(): bool
    {
        return true;
    }
}

class_alias(Type::class, 'eZ\Publish\Core\FieldType\ImageAsset\Type');
