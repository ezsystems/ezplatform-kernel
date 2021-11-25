<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Values\ContentType;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\SPI\Repository\Values\MultiLanguageDescription;
use eZ\Publish\SPI\Repository\Values\MultiLanguageName;

/**
 * this class represents a content type value.
 *
 * @property-read \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[] $contentTypeGroups calls getContentTypeGroups
 * @property-read \eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCollection $fieldDefinitions calls getFieldDefinitions() or on access getFieldDefinition($fieldDefIdentifier)
 * @property-read mixed $id the id of the content type
 * @property-read int $status the status of the content type. One of ContentType::STATUS_DEFINED|ContentType::STATUS_DRAFT|ContentType::STATUS_MODIFIED
 * @property-read string $identifier the identifier of the content type
 * @property-read \DateTime $creationDate the date of the creation of this content type
 * @property-read \DateTime $modificationDate the date of the last modification of this content type
 * @property-read mixed $creatorId the user id of the creator of this content type
 * @property-read mixed $modifierId the user id of the user which has last modified this content type
 * @property-read string $remoteId a global unique id of the content object
 * @property-read string $urlAliasSchema URL alias schema. If nothing is provided, $nameSchema will be used instead.
 * @property-read string $nameSchema  The name schema.
 * @property-read bool $isContainer This flag hints to UIs if type may have children or not.
 * @property-read string $mainLanguageCode the main language of the content type names and description used for fallback.
 * @property-read bool $defaultAlwaysAvailable if an instance of a content type is created the always available flag is set by default this this value.
 * @property-read string[] $languageCodes array of language codes used by content type translations.
 * @property-read int $defaultSortField Specifies which property the child locations should be sorted on by default when created. Valid values are found at {@link Location::SORT_FIELD_*}
 * @property-read int $defaultSortOrder Specifies whether the sort order should be ascending or descending by default when created. Valid values are {@link Location::SORT_ORDER_*}
 */
abstract class ContentType extends ValueObject implements MultiLanguageName, MultiLanguageDescription
{
    /** @var int Status constant for defined (aka "published") Type */
    public const STATUS_DEFINED = 0;

    /** @var int Status constant for draft (aka "temporary") Type */
    public const STATUS_DRAFT = 1;

    /** @var int Status constant for modified (aka "deferred for publishing") Type */
    public const STATUS_MODIFIED = 2;

    /**
     * Content type ID.
     *
     * @var mixed
     */
    protected $id;

    /**
     * The status of the content type.
     *
     * @var int One of Type::STATUS_DEFINED|Type::STATUS_DRAFT|Type::STATUS_MODIFIED
     */
    protected $status;

    /**
     * String identifier of a content type.
     *
     * @var string
     */
    protected $identifier;

    /**
     * Creation date of the content type.
     *
     * @var \DateTime
     */
    protected $creationDate;

    /**
     * Modification date of the content type.
     *
     * @var \DateTime
     */
    protected $modificationDate;

    /**
     * Creator user id of the content type.
     *
     * @var mixed
     */
    protected $creatorId;

    /**
     * Modifier user id of the content type.
     *
     * @var mixed
     */
    protected $modifierId;

    /**
     * Unique remote ID of the content type.
     *
     * @var string
     */
    protected $remoteId;

    /**
     * URL alias schema.
     *
     * If nothing is provided, $nameSchema will be used instead.
     *
     * @var string
     */
    protected $urlAliasSchema;

    /**
     * Name schema.
     *
     * Can be composed of FieldDefinition identifier place holders.
     * These place holders must comply this pattern : <field_definition_identifier>.
     * An OR condition can be used :
     * <field_def|other_field_def>
     * In this example, field_def will be used if available. If not, other_field_def will be used for content name generation
     *
     * @var string
     */
    protected $nameSchema;

    /**
     * A flag used to hint if content of this type may have children or not. It is highly recommended to respect this flag and not create/move content below non-containers.
     * But this flag is not considered as part of the content model and the API will not in any way enforce this flag to be respected.
     *
     * @var bool
     */
    protected $isContainer;

    /**
     * If an instance of a content type is created the always available flag is set
     * by default to this value.
     *
     * @var bool
     */
    protected $defaultAlwaysAvailable = true;

    /**
     * Specifies which property the child locations should be sorted on by default when created.
     *
     * Valid values are found at {@link Location::SORT_FIELD_*}
     *
     * @var int
     */
    protected $defaultSortField;

    /**
     * Specifies whether the sort order should be ascending or descending by default when created.
     *
     * Valid values are {@link Location::SORT_ORDER_*}
     *
     * @var int
     */
    protected $defaultSortOrder;

    /**
     * List of language codes used by translations.
     *
     * @var string[]
     */
    protected $languageCodes;

    /**
     * This method returns the content type groups this content type is assigned to.
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[]
     */
    abstract public function getContentTypeGroups();

    /**
     * This method returns the content type field definitions from this type.
     */
    abstract public function getFieldDefinitions(): FieldDefinitionCollection;

    /**
     * This method returns the field definition for the given identifier.
     *
     * @param string $fieldDefinitionIdentifier
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition|null
     */
    public function getFieldDefinition($fieldDefinitionIdentifier): ?FieldDefinition
    {
        if ($this->hasFieldDefinition($fieldDefinitionIdentifier)) {
            return $this->getFieldDefinitions()->get($fieldDefinitionIdentifier);
        }

        return null;
    }

    /**
     * This method returns true if the field definition for the given identifier exists.
     */
    public function hasFieldDefinition(string $fieldDefinitionIdentifier): bool
    {
        return $this->getFieldDefinitions()->has($fieldDefinitionIdentifier);
    }

    /**
     * Returns true if field definition with given field type identifier exists.
     */
    public function hasFieldDefinitionOfType(string $fieldTypeIdentifier): bool
    {
        return $this->getFieldDefinitions()->anyOfType($fieldTypeIdentifier);
    }

    /**
     * Returns collection of the field definition for the given field type identifier.
     */
    public function getFieldDefinitionsOfType(string $fieldTypeIdentifier): FieldDefinitionCollection
    {
        return $this->getFieldDefinitions()->filterByType($fieldTypeIdentifier);
    }

    /**
     * Returns true if field definition with given field type identifier or null.
     */
    public function getFirstFieldDefinitionOfType(string $fieldTypeIdentifier): ?FieldDefinition
    {
        $fieldDefinitionsOfType = $this->getFieldDefinitionsOfType($fieldTypeIdentifier);
        if (!$fieldDefinitionsOfType->isEmpty()) {
            return $fieldDefinitionsOfType->first();
        }

        return null;
    }
}
