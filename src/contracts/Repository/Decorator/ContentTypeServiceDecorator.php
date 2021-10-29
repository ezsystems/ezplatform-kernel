<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\User;

abstract class ContentTypeServiceDecorator implements ContentTypeService
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    protected $innerService;

    public function __construct(ContentTypeService $innerService)
    {
        $this->innerService = $innerService;
    }

    public function createContentTypeGroup(ContentTypeGroupCreateStruct $contentTypeGroupCreateStruct): ContentTypeGroup
    {
        return $this->innerService->createContentTypeGroup($contentTypeGroupCreateStruct);
    }

    public function loadContentTypeGroup(
        int $contentTypeGroupId,
        array $prioritizedLanguages = []
    ): ContentTypeGroup {
        return $this->innerService->loadContentTypeGroup($contentTypeGroupId, $prioritizedLanguages);
    }

    public function loadContentTypeGroupByIdentifier(
        string $contentTypeGroupIdentifier,
        array $prioritizedLanguages = []
    ): ContentTypeGroup {
        return $this->innerService->loadContentTypeGroupByIdentifier($contentTypeGroupIdentifier, $prioritizedLanguages);
    }

    public function loadContentTypeGroups(array $prioritizedLanguages = []): iterable
    {
        return $this->innerService->loadContentTypeGroups($prioritizedLanguages);
    }

    public function updateContentTypeGroup(
        ContentTypeGroup $contentTypeGroup,
        ContentTypeGroupUpdateStruct $contentTypeGroupUpdateStruct
    ): void {
        $this->innerService->updateContentTypeGroup($contentTypeGroup, $contentTypeGroupUpdateStruct);
    }

    public function deleteContentTypeGroup(ContentTypeGroup $contentTypeGroup): void
    {
        $this->innerService->deleteContentTypeGroup($contentTypeGroup);
    }

    public function createContentType(
        ContentTypeCreateStruct $contentTypeCreateStruct,
        array $contentTypeGroups
    ): ContentTypeDraft {
        return $this->innerService->createContentType($contentTypeCreateStruct, $contentTypeGroups);
    }

    public function loadContentType(
        int $contentTypeId,
        array $prioritizedLanguages = []
    ): ContentType {
        return $this->innerService->loadContentType($contentTypeId, $prioritizedLanguages);
    }

    public function loadContentTypeByIdentifier(
        string $identifier,
        array $prioritizedLanguages = []
    ): ContentType {
        return $this->innerService->loadContentTypeByIdentifier($identifier, $prioritizedLanguages);
    }

    public function loadContentTypeByRemoteId(
        string $remoteId,
        array $prioritizedLanguages = []
    ): ContentType {
        return $this->innerService->loadContentTypeByRemoteId($remoteId, $prioritizedLanguages);
    }

    public function loadContentTypeDraft(int $contentTypeId, bool $ignoreOwnership = false): ContentTypeDraft
    {
        return $this->innerService->loadContentTypeDraft($contentTypeId, $ignoreOwnership);
    }

    public function loadContentTypeList(
        array $contentTypeIds,
        array $prioritizedLanguages = []
    ): iterable {
        return $this->innerService->loadContentTypeList($contentTypeIds, $prioritizedLanguages);
    }

    public function loadContentTypes(
        ContentTypeGroup $contentTypeGroup,
        array $prioritizedLanguages = []
    ): iterable {
        return $this->innerService->loadContentTypes($contentTypeGroup, $prioritizedLanguages);
    }

    public function createContentTypeDraft(ContentType $contentType): ContentTypeDraft
    {
        return $this->innerService->createContentTypeDraft($contentType);
    }

    public function updateContentTypeDraft(
        ContentTypeDraft $contentTypeDraft,
        ContentTypeUpdateStruct $contentTypeUpdateStruct
    ): void {
        $this->innerService->updateContentTypeDraft($contentTypeDraft, $contentTypeUpdateStruct);
    }

    public function deleteContentType(ContentType $contentType): void
    {
        $this->innerService->deleteContentType($contentType);
    }

    public function copyContentType(
        ContentType $contentType,
        User $creator = null
    ): ContentType {
        return $this->innerService->copyContentType($contentType, $creator);
    }

    public function assignContentTypeGroup(
        ContentType $contentType,
        ContentTypeGroup $contentTypeGroup
    ): void {
        $this->innerService->assignContentTypeGroup($contentType, $contentTypeGroup);
    }

    public function unassignContentTypeGroup(
        ContentType $contentType,
        ContentTypeGroup $contentTypeGroup
    ): void {
        $this->innerService->unassignContentTypeGroup($contentType, $contentTypeGroup);
    }

    public function addFieldDefinition(
        ContentTypeDraft $contentTypeDraft,
        FieldDefinitionCreateStruct $fieldDefinitionCreateStruct
    ): void {
        $this->innerService->addFieldDefinition($contentTypeDraft, $fieldDefinitionCreateStruct);
    }

    public function removeFieldDefinition(
        ContentTypeDraft $contentTypeDraft,
        FieldDefinition $fieldDefinition
    ): void {
        $this->innerService->removeFieldDefinition($contentTypeDraft, $fieldDefinition);
    }

    public function updateFieldDefinition(
        ContentTypeDraft $contentTypeDraft,
        FieldDefinition $fieldDefinition,
        FieldDefinitionUpdateStruct $fieldDefinitionUpdateStruct
    ): void {
        $this->innerService->updateFieldDefinition($contentTypeDraft, $fieldDefinition, $fieldDefinitionUpdateStruct);
    }

    public function publishContentTypeDraft(ContentTypeDraft $contentTypeDraft): void
    {
        $this->innerService->publishContentTypeDraft($contentTypeDraft);
    }

    public function newContentTypeGroupCreateStruct(string $identifier): ContentTypeGroupCreateStruct
    {
        return $this->innerService->newContentTypeGroupCreateStruct($identifier);
    }

    public function newContentTypeCreateStruct(string $identifier): ContentTypeCreateStruct
    {
        return $this->innerService->newContentTypeCreateStruct($identifier);
    }

    public function newContentTypeUpdateStruct(): ContentTypeUpdateStruct
    {
        return $this->innerService->newContentTypeUpdateStruct();
    }

    public function newContentTypeGroupUpdateStruct(): ContentTypeGroupUpdateStruct
    {
        return $this->innerService->newContentTypeGroupUpdateStruct();
    }

    public function newFieldDefinitionCreateStruct(
        string $identifier,
        string $fieldTypeIdentifier
    ): FieldDefinitionCreateStruct {
        return $this->innerService->newFieldDefinitionCreateStruct($identifier, $fieldTypeIdentifier);
    }

    public function newFieldDefinitionUpdateStruct(): FieldDefinitionUpdateStruct
    {
        return $this->innerService->newFieldDefinitionUpdateStruct();
    }

    public function isContentTypeUsed(ContentType $contentType): bool
    {
        return $this->innerService->isContentTypeUsed($contentType);
    }

    public function removeContentTypeTranslation(
        ContentTypeDraft $contentTypeDraft,
        string $languageCode
    ): ContentTypeDraft {
        return $this->innerService->removeContentTypeTranslation($contentTypeDraft, $languageCode);
    }

    public function deleteUserDrafts(int $userId): void
    {
        $this->innerService->deleteUserDrafts($userId);
    }
}

class_alias(ContentTypeServiceDecorator::class, 'eZ\Publish\SPI\Repository\Decorator\ContentTypeServiceDecorator');
