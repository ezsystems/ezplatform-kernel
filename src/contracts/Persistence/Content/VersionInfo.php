<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\Content;

use Ibexa\Contracts\Core\Persistence\ValueObject;

/**
 * This class holds version information data.
 */
class VersionInfo extends ValueObject
{
    /**
     * Version status constants.
     *
     * @var int
     */
    public const STATUS_DRAFT = 0;
    public const STATUS_PUBLISHED = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_ARCHIVED = 3;
    public const STATUS_REJECTED = 4;
    public const STATUS_INTERNAL_DRAFT = 5;
    public const STATUS_REPEAT = 6;
    public const STATUS_QUEUED = 7;

    /**
     * Version ID.
     *
     * @var mixed
     */
    public $id;

    /**
     * Version number.
     *
     * In contrast to {@link $id}, this is the version number, which only
     * increments in scope of a single Content object.
     *
     * @var int
     */
    public $versionNo;

    /**
     * ContentInfo of the content this VersionInfo belongs to.
     *
     * @var \Ibexa\Contracts\Core\Persistence\Content\ContentInfo
     */
    public $contentInfo;

    /**
     * Returns the names computed from the name schema in the available languages.
     * Eg. array( 'eng-GB' => "New Article" ).
     *
     * @return string[]
     */
    public $names;

    /**
     * Creation date of this version, as a UNIX timestamp.
     *
     * @var int
     */
    public $creationDate;

    /**
     * Last modified date of this version, as a UNIX timestamp.
     *
     * @var int
     */
    public $modificationDate;

    /**
     * Creator user ID.
     *
     * Creator of the version, in the search API this is referred to as the modifier of the published content.
     *
     * @var int
     */
    public $creatorId;

    /**
     * One of VersionInfo::STATUS_DRAFT, VersionInfo::STATUS_PUBLISHED, VersionInfo::STATUS_ARCHIVED.
     *
     * @var int
     */
    public $status;

    /**
     * In 4.x this is the language code which is used for labeling a translation.
     *
     * @var string
     */
    public $initialLanguageCode;

    /**
     * List of languages in this version.
     *
     * Reflects which languages fields exists in for this version.
     *
     * @var string[]
     */
    public $languageCodes = [];
}

class_alias(VersionInfo::class, 'eZ\Publish\SPI\Persistence\Content\VersionInfo');
