<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Values\Content;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * This class provides all version independent information of the Content object.
 *
 * @property-read int $id The unique id of the Content object
 * @property-read int $contentTypeId The unique id of the Content Type object the Content object is an instance of
 * @property-read string $name the computed name (via name schema) in the main language of the Content object
 * @property-read int $sectionId the section to which the Content object is assigned
 * @property-read int $currentVersionNo Current Version number is the version number of the published version or the version number of a newly created draft (which is 1).
 * @property-read bool $published true if there exists a published version false otherwise
 * @property-read int $ownerId the user id of the owner of the Content object
 * @property-read \DateTime $modificationDate Content object modification date
 * @property-read \DateTime $publishedDate date of the first publish
 * @property-read bool $alwaysAvailable Indicates if the Content object is shown in the mainlanguage if its not present in an other requested language
 * @property-read string $remoteId a global unique id of the Content object
 * @property-read string $mainLanguageCode The main language code of the Content object. If the available flag is set to true the Content is shown in this language if the requested language does not exist.
 * @property-read int|null $mainLocationId Identifier of the main location.
 * @property-read int $status status of the Content object
 * @property-read bool $isHidden status of the Content object
 */
class ContentInfo extends ValueObject
{
    public const STATUS_DRAFT = 0;
    public const STATUS_PUBLISHED = 1;
    public const STATUS_TRASHED = 2;

    /**
     * The unique id of the Content object.
     *
     * @var int
     */
    protected $id;

    /**
     * The Content Type id of the Content object.
     *
     * @var int
     */
    protected $contentTypeId;

    /**
     * The computed name (via name schema) in the main language of the Content object.
     *
     * For names in other languages then main see {@see \eZ\Publish\API\Repository\Values\Content\VersionInfo}
     *
     * @var string
     */
    protected $name;

    /**
     * The section to which the Content object is assigned.
     *
     * @var int
     */
    protected $sectionId;

    /**
     * Current Version number is the version number of the published version or the version number of
     * a newly created draft (which is 1).
     *
     * @var int
     */
    protected $currentVersionNo;

    /**
     * True if there exists a published version, false otherwise.
     *
     * @var bool Constant.
     */
    protected $published;

    /**
     * The owner of the Content object.
     *
     * @var int
     */
    protected $ownerId;

    /**
     * Content modification date.
     *
     * @var \DateTime
     */
    protected $modificationDate;

    /**
     * Content publication date.
     *
     * @var \DateTime
     */
    protected $publishedDate;

    /**
     * Indicates if the Content object is shown in the mainlanguage if its not present in an other requested language.
     *
     * @var bool
     */
    protected $alwaysAvailable;

    /**
     * Remote identifier used as a custom identifier for the object.
     *
     * @var string
     */
    protected $remoteId;

    /**
     * The main language code of the Content object.
     *
     * @var string
     */
    protected $mainLanguageCode;

    /**
     * Identifier of the main location.
     *
     * If the Content object has multiple locations,
     * $mainLocationId will point to the main one.
     *
     * @var int|null
     */
    protected $mainLocationId;

    /**
     * Status of the content.
     *
     * Replaces deprecated API\ContentInfo::$published.
     *
     * @var int
     */
    protected $status;

    /** @var bool */
    protected $isHidden;

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType */
    protected $contentType;

    /** @var \eZ\Publish\API\Repository\Values\Content\Section */
    protected $section;

    /** @var \eZ\Publish\API\Repository\Values\Content\Language */
    protected $mainLanguage;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    protected $mainLocation;

    /** @var \eZ\Publish\API\Repository\Values\User\User */
    protected $owner;

    /**
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * @return bool
     */
    public function isTrashed(): bool
    {
        return $this->status === self::STATUS_TRASHED;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    public function getSection(): Section
    {
        return $this->section;
    }

    public function getMainLanguage(): Language
    {
        return $this->mainLanguage;
    }

    public function getMainLanguageCode(): string
    {
        return $this->mainLanguageCode;
    }

    public function getMainLocation(): ?Location
    {
        return $this->mainLocation;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }
}
