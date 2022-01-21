<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository\Values\Content;

use eZ\Publish\API\Repository\Values\Content\Content as APIContent;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\Thumbnail;
use eZ\Publish\API\Repository\Values\Content\VersionInfo as APIVersionInfo;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\SPI\FieldType\Value;

/**
 * this class represents a content object in a specific version.
 *
 * @property-read \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo convenience getter for $versionInfo->contentInfo
 * @property-read \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType convenience getter for $versionInfo->contentInfo->contentType
 * @property-read int $id convenience getter for retrieving the contentId: $versionInfo->content->id
 * @property-read \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo calls getVersionInfo()
 * @property-read \eZ\Publish\API\Repository\Values\Content\Field[] $fields Access fields, calls getFields()
 *
 * @internal Meant for internal use by Repository, type hint against API object instead.
 */
class Content extends APIContent
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Thumbnail|null */
    protected $thumbnail;

    /** @var mixed[][] An array of array of field values like[$fieldDefIdentifier][$languageCode] */
    protected $fields;

    /** @var \eZ\Publish\API\Repository\Values\Content\VersionInfo */
    protected $versionInfo;

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType */
    protected $contentType;

    /** @var \eZ\Publish\API\Repository\Values\Content\Field[] An array of {@link Field} */
    private $internalFields = [];

    /**
     * The first matched field language among user provided prioritized languages.
     *
     * The first matched language among user provided prioritized languages on object retrieval, or null if none
     * provided (all languages) or on main fallback.
     *
     * @internal
     *
     * @var string|null
     */
    protected $prioritizedFieldLanguageCode;

    public function __construct(array $data = [])
    {
        foreach ($data as $propertyName => $propertyValue) {
            $this->$propertyName = $propertyValue;
        }
        foreach ($this->internalFields as $field) {
            $this->fields[$field->fieldDefIdentifier][$field->languageCode] = $field->value;
        }
    }

    public function getThumbnail(): ?Thumbnail
    {
        return $this->thumbnail;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionInfo(): APIVersionInfo
    {
        return $this->versionInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue(string $fieldDefIdentifier, ?string $languageCode = null): ?Value
    {
        if (null === $languageCode) {
            $languageCode = $this->getDefaultLanguageCode();
        }

        if (isset($this->fields[$fieldDefIdentifier][$languageCode])) {
            return $this->fields[$fieldDefIdentifier][$languageCode];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(): iterable
    {
        return $this->internalFields;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsByLanguage(?string $languageCode = null): iterable
    {
        $fields = [];

        if (null === $languageCode) {
            $languageCode = $this->getDefaultLanguageCode();
        }

        foreach ($this->getFields() as $field) {
            if ($field->languageCode !== $languageCode) {
                continue;
            }
            $fields[$field->fieldDefIdentifier] = $field;
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getField(string $fieldDefIdentifier, ?string $languageCode = null): ?Field
    {
        if (null === $languageCode) {
            $languageCode = $this->getDefaultLanguageCode();
        }

        foreach ($this->getFields() as $field) {
            if ($field->fieldDefIdentifier === $fieldDefIdentifier
                && $field->languageCode === $languageCode) {
                return $field;
            }
        }

        return null;
    }

    public function getDefaultLanguageCode(): string
    {
        return $this->prioritizedFieldLanguageCode ?: $this->versionInfo->contentInfo->mainLanguageCode;
    }

    /**
     * {@inheritdoc}
     */
    protected function getProperties($dynamicProperties = ['id', 'contentInfo'])
    {
        return parent::getProperties($dynamicProperties);
    }

    /**
     * {@inheritdoc}
     */
    public function __get($property)
    {
        switch ($property) {
            case 'id':
                return $this->versionInfo->contentInfo->id;

            case 'contentInfo':
                return $this->versionInfo->contentInfo;

            case 'thumbnail':
                return $this->getThumbnail();
        }

        return parent::__get($property);
    }

    /**
     * {@inheritdoc}
     */
    public function __isset($property)
    {
        if ($property === 'id') {
            return true;
        }

        if ($property === 'contentInfo') {
            return true;
        }

        return parent::__isset($property);
    }
}
