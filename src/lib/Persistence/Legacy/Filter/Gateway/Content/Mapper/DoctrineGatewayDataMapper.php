<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\Gateway\Content\Mapper;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandler;
use Ibexa\Contracts\Core\Persistence\Content\Type\Handler as ContentTypeHandler;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry;
use Ibexa\Core\Persistence\Legacy\Content\Language\MaskGenerator;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use Ibexa\Core\Persistence\Legacy\Filter\Gateway\Content\GatewayDataMapper;

/**
 * @internal for internal use by Content Filtering Doctrine Gateway data mapper.
 */
final class DoctrineGatewayDataMapper implements GatewayDataMapper
{
    /** @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry */
    private $converterRegistry;

    /** @var \Ibexa\Core\Persistence\Legacy\Content\Language\MaskGenerator */
    private $languageMaskGenerator;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Language\Handler */
    private $languageHandler;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Type\Handler */
    private $contentTypeHandler;

    public function __construct(
        LanguageHandler $languageHandler,
        MaskGenerator $languageMaskGenerator,
        ContentTypeHandler $contentTypeHandler,
        ConverterRegistry $converterRegistry
    ) {
        $this->languageMaskGenerator = $languageMaskGenerator;
        $this->languageHandler = $languageHandler;
        $this->contentTypeHandler = $contentTypeHandler;
        $this->converterRegistry = $converterRegistry;
    }

    /**
     * {@inheritdoc}
     *
     * Column names come from query built by
     * {@see \Ibexa\Core\Persistence\Legacy\Filter\Gateway\Content\Doctrine\DoctrineGateway::buildQuery}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function mapRawDataToPersistenceContentItem(array $row): Content\ContentItem
    {
        $contentInfo = $this->mapContentMetadataToPersistenceContentInfo($row);

        $content = $this->mapContentDataToPersistenceContent($row);
        $content->versionInfo->contentInfo = $contentInfo;

        // aiming to utilize in-memory caching
        $contentType = $this->contentTypeHandler->load($contentInfo->contentTypeId);

        return new Content\ContentItem(
            $content,
            $contentInfo,
            $contentType
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function mapContentDataToPersistenceContent(array $row): Content
    {
        $content = new Content();
        $content->versionInfo = $this->mapVersionDataToPersistenceVersionInfo($row);
        $content->fields = $this->mapFieldDataToPersistenceFieldList(
            $row['content_version_fields'],
            $content->versionInfo->versionNo
        );

        return $content;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function mapVersionDataToPersistenceVersionInfo(array $row): Content\VersionInfo
    {
        $versionInfo = new VersionInfo();
        $versionInfo->id = (int)$row['content_version_id'];
        $versionInfo->versionNo = (int)$row['content_version_no'];
        $versionInfo->creatorId = (int)$row['content_version_creator_id'];
        $versionInfo->creationDate = (int)$row['content_version_created'];
        $versionInfo->modificationDate = (int)$row['content_version_modified'];
        $versionInfo->status = (int)$row['content_version_status'];
        $versionInfo->names = $row['content_version_names'];

        // Map language codes
        $versionInfo->languageCodes = $this->languageMaskGenerator->extractLanguageCodesFromMask(
            (int)$row['content_version_language_mask']
        );
        $versionInfo->initialLanguageCode = $this->languageHandler->load(
            (int)$row['content_version_initial_language_id']
        )->languageCode;

        return $versionInfo;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Field[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function mapFieldDataToPersistenceFieldList(
        array $rawVersionFields,
        int $versionNo
    ): array {
        return array_map(
            function (array $row) use ($versionNo) {
                $field = new Field();
                $field->id = (int)$row['field_id'];
                $field->fieldDefinitionId = (int)$row['field_definition_id'];
                $field->type = $row['field_type'];
                $storageValue = $this->mapFieldValueDataToStorageFieldValue($row);
                $field->value = $this->buildFieldValue($storageValue, $field->type);
                $field->languageCode = $row['field_language_code'];
                $field->versionNo = $versionNo;

                return $field;
            },
            $rawVersionFields
        );
    }

    private function mapFieldValueDataToStorageFieldValue(array $row): StorageFieldValue
    {
        $storageValue = new StorageFieldValue();

        // nullable data
        $storageValue->dataFloat = isset($row['field_data_float']) ? (float)$row['field_data_float'] : null;
        $storageValue->dataInt = isset($row['field_data_int']) ? (int)$row['field_data_int'] : null;
        $storageValue->dataText = $row['field_data_text'] ?? null;

        // non-nullable data
        $storageValue->sortKeyInt = (int)$row['field_sort_key_int'];
        $storageValue->sortKeyString = $row['field_sort_key_string'];

        return $storageValue;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function buildFieldValue(
        StorageFieldValue $storageFieldValue,
        string $fieldType
    ): FieldValue {
        $fieldValue = new FieldValue();

        $converter = $this->converterRegistry->getConverter($fieldType);
        $converter->toFieldValue($storageFieldValue, $fieldValue);

        return $fieldValue;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function mapContentMetadataToPersistenceContentInfo(array $row): ContentInfo
    {
        $contentInfo = new ContentInfo();

        $mainLanguage = $this->languageHandler->load((int)$row['content_initial_language_id']);

        $contentInfo->id = (int)$row['content_id'];
        $contentInfo->name = $row['content_name'];
        $contentInfo->contentTypeId = (int)$row['content_type_id'];
        $contentInfo->sectionId = (int)$row['content_section_id'];
        $contentInfo->currentVersionNo = (int)$row['content_current_version'];
        $contentInfo->ownerId = (int)$row['content_owner_id'];
        $contentInfo->publicationDate = (int)$row['content_published'];
        $contentInfo->modificationDate = (int)$row['content_modified'];
        $contentInfo->alwaysAvailable = 1 === ($row['content_language_mask'] & 1);
        $contentInfo->mainLanguageCode = $mainLanguage->languageCode;
        $contentInfo->remoteId = $row['content_remote_id'];
        $contentInfo->mainLocationId = $row['content_main_location_id'] !== null
            ? (int)$row['content_main_location_id']
            : null;
        $contentInfo->status = (int)$row['content_status'];
        $contentInfo->isHidden = (bool)$row['content_is_hidden'];

        // setting deprecated property for BC reasons
        $contentInfo->isPublished = $contentInfo->status === ContentInfo::STATUS_PUBLISHED;

        return $contentInfo;
    }
}

class_alias(DoctrineGatewayDataMapper::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\Gateway\Content\Mapper\DoctrineGatewayDataMapper');
