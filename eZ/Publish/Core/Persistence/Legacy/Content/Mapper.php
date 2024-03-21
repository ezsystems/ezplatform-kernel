<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Persistence\Legacy\Content;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry as Registry;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\SPI\Persistence\Content\CreateStruct;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Language\Handler as LanguageHandler;
use eZ\Publish\SPI\Persistence\Content\Relation;
use eZ\Publish\SPI\Persistence\Content\Relation\CreateStruct as RelationCreateStruct;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as ContentTypeHandler;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

/**
 * Mapper for Content Handler.
 *
 * Performs mapping of Content objects.
 *
 * @phpstan-type TVersionedLanguageFieldDefinitionsMap array<int, array<int, array<string, array<int, \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition>>>>
 * @phpstan-type TVersionedFieldMap array<int, array<int, array<int, \eZ\Publish\SPI\Persistence\Content\Field>>>
 * @phpstan-type TVersionedNameMap array<int, array<int, array<string, array<int, string>>>>
 * @phpstan-type TContentInfoMap array<int, \eZ\Publish\SPI\Persistence\Content\ContentInfo>
 * @phpstan-type TVersionInfoMap array<int, array<int, \eZ\Publish\SPI\Persistence\Content\VersionInfo>>
 */
class Mapper
{
    /**
     * FieldValue converter registry.
     *
     * @var \eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry
     */
    protected $converterRegistry;

    /**
     * Caching language handler.
     *
     * @var \eZ\Publish\SPI\Persistence\Content\Language\Handler
     */
    protected $languageHandler;

    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Type\Handler
     */
    private $contentTypeHandler;

    public function __construct(
        Registry $converterRegistry,
        LanguageHandler $languageHandler,
        ContentTypeHandler $contentTypeHandler
    ) {
        $this->converterRegistry = $converterRegistry;
        $this->languageHandler = $languageHandler;
        $this->contentTypeHandler = $contentTypeHandler;
    }

    /**
     * Creates a Content from the given $struct and $currentVersionNo.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\CreateStruct $struct
     * @param mixed $currentVersionNo
     *
     * @return \eZ\Publish\SPI\Persistence\Content\ContentInfo
     */
    private function createContentInfoFromCreateStruct(CreateStruct $struct, $currentVersionNo = 1)
    {
        $contentInfo = new ContentInfo();

        $contentInfo->id = null;
        $contentInfo->contentTypeId = $struct->typeId;
        $contentInfo->sectionId = $struct->sectionId;
        $contentInfo->ownerId = $struct->ownerId;
        $contentInfo->alwaysAvailable = $struct->alwaysAvailable;
        $contentInfo->remoteId = $struct->remoteId;
        $contentInfo->mainLanguageCode = $this->languageHandler
            ->load(isset($struct->mainLanguageId) ? $struct->mainLanguageId : $struct->initialLanguageId)
            ->languageCode;
        $contentInfo->name = isset($struct->name[$contentInfo->mainLanguageCode])
            ? $struct->name[$contentInfo->mainLanguageCode]
            : '';
        // For drafts published and modified timestamps should be 0
        $contentInfo->publicationDate = 0;
        $contentInfo->modificationDate = 0;
        $contentInfo->currentVersionNo = $currentVersionNo;
        $contentInfo->status = ContentInfo::STATUS_DRAFT;
        $contentInfo->isPublished = false;
        $contentInfo->isHidden = $struct->isHidden;

        return $contentInfo;
    }

    /**
     * Creates a new version for the given $struct and $versionNo.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\CreateStruct $struct
     * @param mixed $versionNo
     *
     * @return \eZ\Publish\SPI\Persistence\Content\VersionInfo
     */
    public function createVersionInfoFromCreateStruct(CreateStruct $struct, $versionNo)
    {
        $versionInfo = new VersionInfo();

        $versionInfo->id = null;
        $versionInfo->contentInfo = $this->createContentInfoFromCreateStruct($struct, $versionNo);
        $versionInfo->versionNo = $versionNo;
        $versionInfo->creatorId = $struct->ownerId;
        $versionInfo->status = VersionInfo::STATUS_DRAFT;
        $versionInfo->initialLanguageCode = $this->languageHandler->load($struct->initialLanguageId)->languageCode;
        $versionInfo->creationDate = $struct->modified;
        $versionInfo->modificationDate = $struct->modified;
        $versionInfo->names = $struct->name;

        $languages = [];
        foreach ($struct->fields as $field) {
            if (!isset($languages[$field->languageCode])) {
                $languages[$field->languageCode] = true;
            }
        }
        $versionInfo->languageCodes = array_keys($languages);

        return $versionInfo;
    }

    /**
     * Creates a new version for the given $content.
     *
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param mixed $versionNo
     * @param mixed $userId
     * @param string|null $languageCode
     *
     * @return \eZ\Publish\SPI\Persistence\Content\VersionInfo
     */
    public function createVersionInfoForContent(Content $content, $versionNo, $userId, ?string $languageCode = null)
    {
        $versionInfo = new VersionInfo();

        $versionInfo->contentInfo = $content->versionInfo->contentInfo;
        $versionInfo->versionNo = $versionNo;
        $versionInfo->creatorId = $userId;
        $versionInfo->status = VersionInfo::STATUS_DRAFT;
        $versionInfo->initialLanguageCode = $languageCode ?? $content->versionInfo->initialLanguageCode;
        $versionInfo->creationDate = time();
        $versionInfo->modificationDate = $versionInfo->creationDate;
        $versionInfo->names = is_object($content->versionInfo) ? $content->versionInfo->names : [];
        $versionInfo->languageCodes = $content->versionInfo->languageCodes;

        return $versionInfo;
    }

    /**
     * Converts value of $field to storage value.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     *
     * @return \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue
     */
    public function convertToStorageValue(Field $field)
    {
        $converter = $this->converterRegistry->getConverter(
            $field->type
        );
        $storageValue = new StorageFieldValue();
        $converter->toStorageValue(
            $field->value,
            $storageValue
        );

        return $storageValue;
    }

    /**
     * Extracts Content objects (and nested) from database result $rows.
     *
     * Expects database rows to be indexed by keys of the format
     *
     *      "$tableName_$columnName"
     *
     * @param array $rows
     * @param array $nameRows
     * @param string $prefix
     *
     * @return \eZ\Publish\SPI\Persistence\Content[]
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function extractContentFromRows(
        array $rows,
        array $nameRows,
        string $prefix = 'ezcontentobject_'
    ): array {
        $versionedNameData = [];

        foreach ($nameRows as $row) {
            $contentId = (int)$row["{$prefix}name_contentobject_id"];
            $versionNo = (int)$row["{$prefix}name_content_version"];
            $languageCode = $row["{$prefix}name_content_translation"];
            $versionedNameData[$contentId][$versionNo][$languageCode] = $row["{$prefix}name_name"];
        }

        $contentInfos = [];
        $versionInfos = [];
        $fields = [];

        $fieldDefinitions = $this->loadCachedVersionFieldDefinitionsPerLanguage(
            $rows,
            $prefix
        );

        foreach ($rows as $row) {
            $contentId = (int)$row["{$prefix}id"];
            $versionId = (int)$row["{$prefix}version_id"];

            if (!isset($contentInfos[$contentId])) {
                $contentInfos[$contentId] = $this->extractContentInfoFromRow($row, $prefix);
            }

            if (!isset($versionInfos[$contentId])) {
                $versionInfos[$contentId] = [];
            }

            if (!isset($versionInfos[$contentId][$versionId])) {
                $versionInfos[$contentId][$versionId] = $this->extractVersionInfoFromRow($row);
            }

            $fieldId = (int)$row["{$prefix}attribute_id"];
            $fieldDefinitionId = (int)$row["{$prefix}attribute_contentclassattribute_id"];
            $languageCode = $row["{$prefix}attribute_language_code"];

            if (!isset($fields[$contentId][$versionId][$fieldId])
                && isset($fieldDefinitions[$contentId][$versionId][$languageCode][$fieldDefinitionId])
            ) {
                $fields[$contentId][$versionId][$fieldId] = $this->extractFieldFromRow($row);
                unset($fieldDefinitions[$contentId][$versionId][$languageCode][$fieldDefinitionId]);
            }
        }

        return $this->buildContentObjects(
            $contentInfos,
            $versionInfos,
            $fields,
            $fieldDefinitions,
            $versionedNameData
        );
    }

    /**
     * @phpstan-param TContentInfoMap $contentInfos
     * @phpstan-param TVersionInfoMap $versionInfos
     * @phpstan-param TVersionedFieldMap $fields
     * @phpstan-param TVersionedLanguageFieldDefinitionsMap $missingFieldDefinitions
     * @phpstan-param TVersionedNameMap $versionedNames
     *
     * @return \eZ\Publish\SPI\Persistence\Content[]
     */
    private function buildContentObjects(
        array $contentInfos,
        array $versionInfos,
        array $fields,
        array $missingFieldDefinitions,
        array $versionedNames
    ): array {
        $results = [];

        foreach ($contentInfos as $contentId => $contentInfo) {
            foreach ($versionInfos[$contentId] as $versionId => $versionInfo) {
                // Fallback to just main language name if versioned name data is missing
                $names = $versionedNames[$contentId][$versionInfo->versionNo]
                    ?? [$contentInfo->mainLanguageCode => $contentInfo->name];

                $content = new Content();
                $content->versionInfo = $versionInfo;
                $content->versionInfo->names = $names;
                $content->versionInfo->contentInfo = $contentInfo;
                $content->fields = array_values($fields[$contentId][$versionId]);

                $missingVersionFieldDefinitions = $missingFieldDefinitions[$contentId][$versionId];
                foreach ($missingVersionFieldDefinitions as $languageCode => $versionFieldDefinitions) {
                    foreach ($versionFieldDefinitions as $fieldDefinition) {
                        $content->fields[] = $this->createEmptyField(
                            $fieldDefinition,
                            $languageCode
                        );
                    }
                }

                $results[] = $content;
            }
        }

        return $results;
    }

    /**
     * @phpstan-return TVersionedLanguageFieldDefinitionsMap
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function loadCachedVersionFieldDefinitionsPerLanguage(
        array $rows,
        string $prefix
    ): array {
        $fieldDefinitions = [];
        $contentTypes = [];
        $allLanguages = $this->loadAllLanguagesWithIdKey();

        foreach ($rows as $row) {
            $contentId = (int)$row["{$prefix}id"];
            $versionId = (int)$row["{$prefix}version_id"];
            $contentTypeId = (int)$row["{$prefix}contentclass_id"];
            $languageMask = (int)$row["{$prefix}version_language_mask"];

            if (isset($fieldDefinitions[$contentId][$versionId])) {
                continue;
            }

            $languageCodes = $this->extractLanguageCodesFromMask($languageMask, $allLanguages);
            $contentType = $contentTypes[$contentTypeId] = $contentTypes[$contentTypeId]
                ?? $this->contentTypeHandler->load($contentTypeId);
            foreach ($contentType->fieldDefinitions as $fieldDefinition) {
                foreach ($languageCodes as $languageCode) {
                    $id = $fieldDefinition->id;
                    $fieldDefinitions[$contentId][$versionId][$languageCode][$id] = $fieldDefinition;
                }
            }
        }

        return $fieldDefinitions;
    }

    /**
     * Extracts a ContentInfo object from $row.
     *
     * @param array $row
     * @param string $prefix Prefix for row keys, which are initially mapped by ezcontentobject fields
     * @param string $treePrefix Prefix for tree row key, which are initially mapped by ezcontentobject_tree_ fields
     *
     * @return \eZ\Publish\SPI\Persistence\Content\ContentInfo
     */
    public function extractContentInfoFromRow(array $row, $prefix = '', $treePrefix = 'ezcontentobject_tree_')
    {
        $contentInfo = new ContentInfo();
        $contentInfo->id = (int)$row["{$prefix}id"];
        $contentInfo->name = $row["{$prefix}name"];
        $contentInfo->contentTypeId = (int)$row["{$prefix}contentclass_id"];
        $contentInfo->sectionId = (int)$row["{$prefix}section_id"];
        $contentInfo->currentVersionNo = (int)$row["{$prefix}current_version"];
        $contentInfo->ownerId = (int)$row["{$prefix}owner_id"];
        $contentInfo->publicationDate = (int)$row["{$prefix}published"];
        $contentInfo->modificationDate = (int)$row["{$prefix}modified"];
        $contentInfo->alwaysAvailable = 1 === ($row["{$prefix}language_mask"] & 1);
        $contentInfo->mainLanguageCode = $this->languageHandler->load($row["{$prefix}initial_language_id"])->languageCode;
        $contentInfo->remoteId = $row["{$prefix}remote_id"];
        $contentInfo->mainLocationId = ($row["{$treePrefix}main_node_id"] !== null ? (int)$row["{$treePrefix}main_node_id"] : null);
        $contentInfo->status = (int)$row["{$prefix}status"];
        $contentInfo->isPublished = ($contentInfo->status == ContentInfo::STATUS_PUBLISHED);
        $contentInfo->isHidden = (bool)$row["{$prefix}is_hidden"];

        return $contentInfo;
    }

    /**
     * Extracts ContentInfo objects from $rows.
     *
     * @param array $rows
     * @param string $prefix Prefix for row keys, which are initially mapped by ezcontentobject fields
     * @param string $treePrefix Prefix for tree row key, which are initially mapped by ezcontentobject_tree_ fields
     *
     * @return \eZ\Publish\SPI\Persistence\Content\ContentInfo[]
     */
    public function extractContentInfoFromRows(array $rows, $prefix = '', $treePrefix = 'ezcontentobject_tree_')
    {
        $contentInfoObjects = [];
        foreach ($rows as $row) {
            $contentInfoObjects[] = $this->extractContentInfoFromRow($row, $prefix, $treePrefix);
        }

        return $contentInfoObjects;
    }

    /**
     * Extracts a VersionInfo object from $row.
     *
     * This method will return VersionInfo with incomplete data. It is intended to be used only by
     * {@link self::extractContentFromRows} where missing data will be filled in.
     *
     * @param array $row
     * @param array $names
     *
     * @return \eZ\Publish\SPI\Persistence\Content\VersionInfo
     */
    private function extractVersionInfoFromRow(array $row, array $names = [])
    {
        $versionInfo = new VersionInfo();
        $versionInfo->id = (int)$row['ezcontentobject_version_id'];
        $versionInfo->contentInfo = null;
        $versionInfo->versionNo = (int)$row['ezcontentobject_version_version'];
        $versionInfo->creatorId = (int)$row['ezcontentobject_version_creator_id'];
        $versionInfo->creationDate = (int)$row['ezcontentobject_version_created'];
        $versionInfo->modificationDate = (int)$row['ezcontentobject_version_modified'];
        $versionInfo->status = (int)$row['ezcontentobject_version_status'];
        $versionInfo->names = $names;

        // Map language codes
        $allLanguages = $this->loadAllLanguagesWithIdKey();
        $versionInfo->languageCodes = $this->extractLanguageCodesFromMask(
            (int)$row['ezcontentobject_version_language_mask'],
            $allLanguages,
            $missing
        );
        $initialLanguageId = (int)$row['ezcontentobject_version_initial_language_id'];
        if (isset($allLanguages[$initialLanguageId])) {
            $versionInfo->initialLanguageCode = $allLanguages[$initialLanguageId]->languageCode;
        } else {
            $missing[] = $initialLanguageId;
        }

        if (!empty($missing)) {
            throw new NotFoundException(
                'Language',
                implode(', ', $missing) . "' when building content '" . $row['ezcontentobject_id']
            );
        }

        return $versionInfo;
    }

    /**
     * Extracts a VersionInfo object from $row.
     *
     * @param array $rows
     * @param array $nameRows
     *
     * @return \eZ\Publish\SPI\Persistence\Content\VersionInfo[]
     */
    public function extractVersionInfoListFromRows(array $rows, array $nameRows)
    {
        $nameData = [];
        foreach ($nameRows as $row) {
            $versionId = $row['ezcontentobject_name_contentobject_id'] . '_' . $row['ezcontentobject_name_content_version'];
            $nameData[$versionId][$row['ezcontentobject_name_content_translation']] = $row['ezcontentobject_name_name'];
        }

        $allLanguages = $this->loadAllLanguagesWithIdKey();
        $versionInfoList = [];
        foreach ($rows as $row) {
            $versionId = $row['ezcontentobject_id'] . '_' . $row['ezcontentobject_version_version'];
            if (!isset($versionInfoList[$versionId])) {
                $versionInfo = new VersionInfo();
                $versionInfo->id = (int)$row['ezcontentobject_version_id'];
                $versionInfo->contentInfo = $this->extractContentInfoFromRow($row, 'ezcontentobject_');
                $versionInfo->versionNo = (int)$row['ezcontentobject_version_version'];
                $versionInfo->creatorId = (int)$row['ezcontentobject_version_creator_id'];
                $versionInfo->creationDate = (int)$row['ezcontentobject_version_created'];
                $versionInfo->modificationDate = (int)$row['ezcontentobject_version_modified'];
                $versionInfo->status = (int)$row['ezcontentobject_version_status'];
                $versionInfo->names = $nameData[$versionId];
                $versionInfoList[$versionId] = $versionInfo;
                $versionInfo->languageCodes = $this->extractLanguageCodesFromMask(
                    (int)$row['ezcontentobject_version_language_mask'],
                    $allLanguages,
                    $missing
                );
                $initialLanguageId = (int)$row['ezcontentobject_version_initial_language_id'];
                if (isset($allLanguages[$initialLanguageId])) {
                    $versionInfo->initialLanguageCode = $allLanguages[$initialLanguageId]->languageCode;
                } else {
                    $missing[] = $initialLanguageId;
                }

                if (!empty($missing)) {
                    throw new NotFoundException(
                        'Language',
                        implode(', ', $missing) . "' when building content '" . $row['ezcontentobject_id']
                    );
                }
            }
        }

        return array_values($versionInfoList);
    }

    /**
     * @param int $languageMask
     * @param \eZ\Publish\SPI\Persistence\Content\Language[] $allLanguages
     * @param int[] &$missing
     *
     * @return string[]
     */
    private function extractLanguageCodesFromMask(int $languageMask, array $allLanguages, &$missing = [])
    {
        $exp = 2;
        $result = [];

        // Decomposition of $languageMask into its binary components to extract language codes
        while ($exp <= $languageMask) {
            if ($languageMask & $exp) {
                if (isset($allLanguages[$exp])) {
                    $result[] = $allLanguages[$exp]->languageCode;
                } else {
                    $missing[] = $exp;
                }
            }

            $exp *= 2;
        }

        return $result;
    }

    /**
     * @return \eZ\Publish\SPI\Persistence\Content\Language[]
     */
    private function loadAllLanguagesWithIdKey()
    {
        $languagesById = [];
        foreach ($this->languageHandler->loadAll() as $language) {
            $languagesById[$language->id] = $language;
        }

        return $languagesById;
    }

    /**
     * Extracts a Field from $row.
     *
     * @param array $row
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Field
     */
    protected function extractFieldFromRow(array $row)
    {
        $field = new Field();

        $field->id = (int)$row['ezcontentobject_attribute_id'];
        $field->fieldDefinitionId = (int)$row['ezcontentobject_attribute_contentclassattribute_id'];
        $field->type = $row['ezcontentobject_attribute_data_type_string'];
        $field->value = $this->extractFieldValueFromRow($row, $field->type);
        $field->languageCode = $row['ezcontentobject_attribute_language_code'];
        $field->versionNo = isset($row['ezcontentobject_version_version']) ?
            (int)$row['ezcontentobject_version_version'] :
            (int)$row['ezcontentobject_attribute_version'];

        return $field;
    }

    /**
     * Extracts a FieldValue of $type from $row.
     *
     * @param array $row
     * @param string $type
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
     *
     * @throws \eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\Exception\NotFound
     *         if the necessary converter for $type could not be found.
     */
    protected function extractFieldValueFromRow(array $row, $type)
    {
        $storageValue = new StorageFieldValue();

        // Nullable field
        $storageValue->dataFloat = isset($row['ezcontentobject_attribute_data_float'])
            ? (float)$row['ezcontentobject_attribute_data_float']
            : null;
        // Nullable field
        $storageValue->dataInt = isset($row['ezcontentobject_attribute_data_int'])
            ? (int)$row['ezcontentobject_attribute_data_int']
            : null;
        $storageValue->dataText = $row['ezcontentobject_attribute_data_text'];
        // Not nullable field
        $storageValue->sortKeyInt = (int)$row['ezcontentobject_attribute_sort_key_int'];
        $storageValue->sortKeyString = $row['ezcontentobject_attribute_sort_key_string'];

        $fieldValue = new FieldValue();

        $converter = $this->converterRegistry->getConverter($type);
        $converter->toFieldValue($storageValue, $fieldValue);

        return $fieldValue;
    }

    /**
     * Creates CreateStruct from $content.
     *
     * @param \eZ\Publish\SPI\Persistence\Content $content
     *
     * @return \eZ\Publish\SPI\Persistence\Content\CreateStruct
     */
    public function createCreateStructFromContent(Content $content)
    {
        $struct = new CreateStruct();
        $struct->name = $content->versionInfo->names;
        $struct->typeId = $content->versionInfo->contentInfo->contentTypeId;
        $struct->sectionId = $content->versionInfo->contentInfo->sectionId;
        $struct->ownerId = $content->versionInfo->contentInfo->ownerId;
        $struct->locations = [];
        $struct->alwaysAvailable = $content->versionInfo->contentInfo->alwaysAvailable;
        $struct->remoteId = md5(uniqid(static::class, true));
        $struct->initialLanguageId = $this->languageHandler->loadByLanguageCode($content->versionInfo->initialLanguageCode)->id;
        $struct->mainLanguageId = $this->languageHandler->loadByLanguageCode($content->versionInfo->contentInfo->mainLanguageCode)->id;
        $struct->modified = time();
        $struct->isHidden = $content->versionInfo->contentInfo->isHidden;

        foreach ($content->fields as $field) {
            $newField = clone $field;
            $newField->id = null;
            $struct->fields[] = $newField;
        }

        return $struct;
    }

    /**
     * Extracts relation objects from $rows.
     */
    public function extractRelationsFromRows(array $rows)
    {
        $relations = [];

        foreach ($rows as $row) {
            $id = (int)$row['ezcontentobject_link_id'];
            if (!isset($relations[$id])) {
                $relations[$id] = $this->extractRelationFromRow($row);
            }
        }

        return $relations;
    }

    /**
     * Extracts a Relation object from a $row.
     *
     * @param array $row Associative array representing a relation
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Relation
     */
    protected function extractRelationFromRow(array $row)
    {
        $relation = new Relation();
        $relation->id = (int)$row['ezcontentobject_link_id'];
        $relation->sourceContentId = (int)$row['ezcontentobject_link_from_contentobject_id'];
        $relation->sourceContentVersionNo = (int)$row['ezcontentobject_link_from_contentobject_version'];
        $relation->destinationContentId = (int)$row['ezcontentobject_link_to_contentobject_id'];
        $relation->type = (int)$row['ezcontentobject_link_relation_type'];

        $contentClassAttributeId = (int)$row['ezcontentobject_link_contentclassattribute_id'];
        if ($contentClassAttributeId > 0) {
            $relation->sourceFieldDefinitionId = $contentClassAttributeId;
        }

        return $relation;
    }

    /**
     * Creates a Content from the given $struct.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Relation\CreateStruct $struct
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Relation
     */
    public function createRelationFromCreateStruct(RelationCreateStruct $struct)
    {
        $relation = new Relation();

        $relation->destinationContentId = $struct->destinationContentId;
        $relation->sourceContentId = $struct->sourceContentId;
        $relation->sourceContentVersionNo = $struct->sourceContentVersionNo;
        $relation->sourceFieldDefinitionId = $struct->sourceFieldDefinitionId;
        $relation->type = $struct->type;

        return $relation;
    }

    private function createEmptyField(FieldDefinition $fieldDefinition, string $languageCode): Field
    {
        $field = new Field();
        $field->fieldDefinitionId = $fieldDefinition->id;
        $field->type = $fieldDefinition->fieldType;
        $field->value = $this->getDefaultValue($fieldDefinition);
        $field->languageCode = $languageCode;

        return $field;
    }

    private function getDefaultValue(FieldDefinition $fieldDefinition): FieldValue
    {
        $value = clone $fieldDefinition->defaultValue;
        $storageValue = $this->getDefaultStorageValue();

        $converter = $this->converterRegistry->getConverter($fieldDefinition->fieldType);
        $converter->toStorageValue($value, $storageValue);
        $converter->toFieldValue($storageValue, $value);

        return $value;
    }

    private function getDefaultStorageValue(): StorageFieldValue
    {
        $storageValue = new StorageFieldValue();
        $storageValue->dataFloat = null;
        $storageValue->dataInt = null;
        $storageValue->dataText = '';
        $storageValue->sortKeyInt = 0;
        $storageValue->sortKeyString = '';

        return $storageValue;
    }
}
