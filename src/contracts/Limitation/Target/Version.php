<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Limitation\Target;

use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Persistence\ValueObject;

/**
 * Version Limitation target. Indicates an intent to create new Version.
 *
 * @property-read string[] $allLanguageCodesList
 * @property-read int[] $allContentTypeIdsList
 * @property-read int $newStatus
 * @property-read string $forUpdateInitialLanguageCode
 * @property-read string[] $forUpdateLanguageCodesList
 * @property-read string[] $forPublishLanguageCodesList
 * @property-read \Ibexa\Contracts\Core\Repository\Values\Content\Field[] $updatedFields
 */
final class Version extends ValueObject implements Target
{
    /**
     * List of language codes of translations. At least one must match Limitation values.
     *
     * @var string[]
     */
    protected $allLanguageCodesList = [];

    /**
     * List of content types. At least one must match Limitation values.
     *
     * @var int[]
     */
    protected $allContentTypeIdsList = [];

    /**
     * Language code of a translation used when updated, can be null for e.g. multiple translations changed.
     *
     * @var string|null
     */
    protected $forUpdateInitialLanguageCode;

    /**
     * List of language codes of translations to update. All must match Limitation values.
     *
     * @var string[]
     */
    protected $forUpdateLanguageCodesList = [];

    /**
     * List of language codes of translations to publish. All must match Limitation values.
     *
     * @var string[]
     */
    protected $forPublishLanguageCodesList = [];

    /**
     * One of the following: STATUS_DRAFT, STATUS_PUBLISHED, STATUS_ARCHIVED.
     *
     * @see \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo::STATUS_DRAFT
     * @see \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo::STATUS_PUBLISHED
     * @see \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo::STATUS_ARCHIVED
     *
     * @var int|null
     */
    protected $newStatus;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field[] */
    protected $updatedFields = [];

    /**
     * List of language codes of translations to delete. All must match Limitation values.
     *
     * @var string[]
     */
    private $translationsToDelete = [];

    /**
     * @param string[] $translationsToDelete List of language codes of translations to delete
     */
    public function deleteTranslations(array $translationsToDelete): self
    {
        $this->translationsToDelete = $translationsToDelete;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTranslationsToDelete(): array
    {
        return $this->translationsToDelete;
    }
}

class_alias(Version::class, 'eZ\Publish\SPI\Limitation\Target\Version');
