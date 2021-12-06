<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository;

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

interface ContentTypeService
{
    /**
     * Create a Content Type Group object.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to create a content type group
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If a group with the same identifier already exists
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupCreateStruct $contentTypeGroupCreateStruct
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup
     */
    public function createContentTypeGroup(ContentTypeGroupCreateStruct $contentTypeGroupCreateStruct): ContentTypeGroup;

    /**
     * Get a Content Type Group object by id.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If group can not be found
     *
     * @param int $contentTypeGroupId
     * @param string[] $prioritizedLanguages Used as prioritized language code on translated properties of returned object.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup
     */
    public function loadContentTypeGroup(int $contentTypeGroupId, array $prioritizedLanguages = []): ContentTypeGroup;

    /**
     * Get a Content Type Group object by identifier.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If group can not be found
     *
     * @param string $contentTypeGroupIdentifier
     * @param string[] $prioritizedLanguages Used as prioritized language code on translated properties of returned object.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup
     */
    public function loadContentTypeGroupByIdentifier(string $contentTypeGroupIdentifier, array $prioritizedLanguages = []): ContentTypeGroup;

    /**
     * Get all Content Type Groups.
     *
     * @param string[] $prioritizedLanguages Used as prioritized language code on translated properties of returned object.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup[]
     */
    public function loadContentTypeGroups(array $prioritizedLanguages = []): iterable;

    /**
     * Update a Content Type Group object.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to create a content type group
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the given identifier (if set) already exists
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup the content type group to be updated
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupUpdateStruct $contentTypeGroupUpdateStruct
     */
    public function updateContentTypeGroup(ContentTypeGroup $contentTypeGroup, ContentTypeGroupUpdateStruct $contentTypeGroupUpdateStruct): void;

    /**
     * Delete a Content Type Group.
     *
     * This method only deletes an content type group which has content types without any content instances
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to delete a content type group
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If  a to be deleted content type has instances
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
     */
    public function deleteContentTypeGroup(ContentTypeGroup $contentTypeGroup): void;

    /**
     * Create a Content Type object.
     *
     * The content type is created in the state STATUS_DRAFT.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to create a content type
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException In case when
     *         - array of content type groups does not contain at least one content type group
     *         - identifier or remoteId in the content type create struct already exists
     *         - there is a duplicate field identifier in the content type create struct
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentTypeFieldDefinitionValidationException
     *         if a field definition in the $contentTypeCreateStruct is not valid
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeCreateStruct $contentTypeCreateStruct
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup[] $contentTypeGroups Required array of
     *        {@link ContentTypeGroup} to link type with (must contain one)
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft
     */
    public function createContentType(ContentTypeCreateStruct $contentTypeCreateStruct, array $contentTypeGroups): ContentTypeDraft;

    /**
     * Get a Content Type object by id.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If a content type with the given id and status DEFINED can not be found
     *
     * @param int $contentTypeId
     * @param string[] $prioritizedLanguages Used as prioritized language code on translated properties of returned object.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
     */
    public function loadContentType(int $contentTypeId, array $prioritizedLanguages = []): ContentType;

    /**
     * Get a Content Type object by identifier.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If content type with the given identifier and status DEFINED can not be found
     *
     * @param string $identifier
     * @param string[] $prioritizedLanguages Used as prioritized language code on translated properties of returned object.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
     */
    public function loadContentTypeByIdentifier(string $identifier, array $prioritizedLanguages = []): ContentType;

    /**
     * Get a Content Type object by id.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If content type with the given remote id and status DEFINED can not be found
     *
     * @param string $remoteId
     * @param string[] $prioritizedLanguages Used as prioritized language code on translated properties of returned object.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
     */
    public function loadContentTypeByRemoteId(string $remoteId, array $prioritizedLanguages = []): ContentType;

    /**
     * Get a Content Type object draft by id.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If the content type draft owned by the current user can not be found
     *
     * @param int $contentTypeId
     * @param bool $ignoreOwnership if true, method will return draft even if the owner is different than currently logged in user
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft
     */
    public function loadContentTypeDraft(int $contentTypeId, bool $ignoreOwnership = false): ContentTypeDraft;

    /**
     * Bulk-load Content Type objects by ids.
     *
     * Note: it does not throw exceptions on load, just ignores erroneous items.
     *
     * @since 7.3
     *
     * @param int[] $contentTypeIds
     * @param string[] $prioritizedLanguages Used as prioritized language code on translated properties of returned object.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType[]|iterable
     */
    public function loadContentTypeList(array $contentTypeIds, array $prioritizedLanguages = []): iterable;

    /**
     * Get Content Type objects which belong to the given content type group.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
     * @param string[] $prioritizedLanguages Used as prioritized language code on translated properties of returned object.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType[] an array of {@link ContentType} which have status DEFINED
     */
    public function loadContentTypes(ContentTypeGroup $contentTypeGroup, array $prioritizedLanguages = []): iterable;

    /**
     * Creates a draft from an existing content type.
     *
     * This is a complete copy of the content
     * type which has the state STATUS_DRAFT.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to edit a content type
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException If there is already a draft assigned to another user
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft
     */
    public function createContentTypeDraft(ContentType $contentType): ContentTypeDraft;

    /**
     * Update a Content Type object.
     *
     * Does not update fields (fieldDefinitions), use {@link updateFieldDefinition()} to update them.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to update a content type
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the given identifier or remoteId already exists.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct $contentTypeUpdateStruct
     */
    public function updateContentTypeDraft(ContentTypeDraft $contentTypeDraft, ContentTypeUpdateStruct $contentTypeUpdateStruct): void;

    /**
     * Delete a Content Type object.
     *
     * Deletes a content type if it has no instances. If content type in state STATUS_DRAFT is
     * given, only the draft content type will be deleted. Otherwise, if content type in state
     * STATUS_DEFINED is given, all content type data will be deleted.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException If there exist content objects of this type
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to delete a content type
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     */
    public function deleteContentType(ContentType $contentType): void;

    /**
     * Copy Type incl fields and groupIds to a new Type object.
     *
     * New Type will have $creator as creator / modifier, created / modified should be updated with current time,
     * updated remoteId and identifier should be appended with '_' + unique string.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the current-user is not allowed to copy a content type
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     * @param \Ibexa\Contracts\Core\Repository\Values\User\User $creator If null the current-user is used instead
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
     */
    public function copyContentType(ContentType $contentType, User $creator = null): ContentType;

    /**
     * Assigns a content type to a content type group.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to unlink a content type
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the content type is already assigned the given group
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
     */
    public function assignContentTypeGroup(ContentType $contentType, ContentTypeGroup $contentTypeGroup): void;

    /**
     * Unassign a content type from a group.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to link a content type
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the content type is not assigned this the given group.
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException If $contentTypeGroup is the last group assigned to the content type
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup $contentTypeGroup
     */
    public function unassignContentTypeGroup(ContentType $contentType, ContentTypeGroup $contentTypeGroup): void;

    /**
     * Adds a new field definition to an existing content type.
     *
     * The content type must be in state DRAFT.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException if the identifier in already exists in the content type
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to edit a content type
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentTypeFieldDefinitionValidationException
     *         if a field definition in the $contentTypeCreateStruct is not valid
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException If field definition of the same non-repeatable type is being
     *                                                                 added to the ContentType that already contains one
     *                                                                 or field definition that can't be added to a ContentType that
     *                                                                 has Content instances is being added to such ContentType
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct $fieldDefinitionCreateStruct
     */
    public function addFieldDefinition(ContentTypeDraft $contentTypeDraft, FieldDefinitionCreateStruct $fieldDefinitionCreateStruct): void;

    /**
     * Remove a field definition from an existing Type.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the given field definition does not belong to the given type
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to edit a content type
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     */
    public function removeFieldDefinition(ContentTypeDraft $contentTypeDraft, FieldDefinition $fieldDefinition): void;

    /**
     * Update a field definition.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the field id in the update struct is not found or does not belong to the content type of
     *                                                                        If the given identifier is used in an existing field of the given content type
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to edit a content type
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft the content type draft
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition the field definition which should be updated
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionUpdateStruct $fieldDefinitionUpdateStruct
     */
    public function updateFieldDefinition(ContentTypeDraft $contentTypeDraft, FieldDefinition $fieldDefinition, FieldDefinitionUpdateStruct $fieldDefinitionUpdateStruct): void;

    /**
     * Publish the content type and update content objects.
     *
     * This method updates content objects, depending on the changed field definitions.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException If the content type has no draft
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the content type has no field definitions
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException if the user is not allowed to publish a content type
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft
     */
    public function publishContentTypeDraft(ContentTypeDraft $contentTypeDraft): void;

    /**
     * Instantiates a new content type group create class.
     *
     * @param string $identifier
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupCreateStruct
     */
    public function newContentTypeGroupCreateStruct(string $identifier): ContentTypeGroupCreateStruct;

    /**
     * Instantiates a new content type create class.
     *
     * @param string $identifier
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeCreateStruct
     */
    public function newContentTypeCreateStruct(string $identifier): ContentTypeCreateStruct;

    /**
     * Instantiates a new content type update struct.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct
     */
    public function newContentTypeUpdateStruct(): ContentTypeUpdateStruct;

    /**
     * Instantiates a new content type update struct.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupUpdateStruct
     */
    public function newContentTypeGroupUpdateStruct(): ContentTypeGroupUpdateStruct;

    /**
     * Instantiates a field definition create struct.
     *
     * @param string $fieldTypeIdentifier the required field type identifier
     * @param string $identifier the required identifier for the field definition
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct
     */
    public function newFieldDefinitionCreateStruct(string $identifier, string $fieldTypeIdentifier): FieldDefinitionCreateStruct;

    /**
     * Instantiates a field definition update class.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionUpdateStruct
     */
    public function newFieldDefinitionUpdateStruct(): FieldDefinitionUpdateStruct;

    /**
     * Returns true if the given content type $contentType has content instances.
     *
     * @since 6.0.1
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @return bool
     */
    public function isContentTypeUsed(ContentType $contentType): bool;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft
     * @param string $languageCode
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft
     */
    public function removeContentTypeTranslation(ContentTypeDraft $contentTypeDraft, string $languageCode): ContentTypeDraft;

    /**
     * Delete all content type drafs created or modified by the user.
     *
     * @param int $userId
     */
    public function deleteUserDrafts(int $userId): void;
}

class_alias(ContentTypeService::class, 'eZ\Publish\API\Repository\ContentTypeService');
