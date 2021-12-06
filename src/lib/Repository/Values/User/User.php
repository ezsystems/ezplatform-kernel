<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Values\User;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo as APIVersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\User as APIUser;

/**
 * This class represents a user value.
 *
 * @internal Meant for internal use by Repository, type hint against API object instead.
 */
class User extends APIUser
{
    /**
     * Internal content representation.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    protected $content;

    /**
     * Returns the VersionInfo for this version.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo
     */
    public function getVersionInfo(): APIVersionInfo
    {
        return $this->content->getVersionInfo();
    }

    public function getContentType(): ContentType
    {
        return $this->content->getContentType();
    }

    /**
     * Returns a field value for the given value
     * $version->fields[$fieldDefId][$languageCode] is an equivalent call
     * if no language is given on a translatable field this method returns
     * the value of the initial language of the version if present, otherwise null.
     * On non translatable fields this method ignores the languageCode parameter.
     *
     * @param string $fieldDefIdentifier
     * @param string $languageCode
     *
     * @return mixed a primitive type or a field type Value object depending on the field type.
     */
    public function getFieldValue(string $fieldDefIdentifier, ?string $languageCode = null): ?Value
    {
        return $this->content->getFieldValue($fieldDefIdentifier, $languageCode);
    }

    /**
     * This method returns the complete fields collection.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Field[]
     */
    public function getFields(): iterable
    {
        return $this->content->getFields();
    }

    /**
     * This method returns the fields for a given language and non translatable fields.
     *
     * If note set the initialLanguage of the content version is used.
     *
     * @param string $languageCode
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Field[] with field identifier as keys
     */
    public function getFieldsByLanguage(?string $languageCode = null): iterable
    {
        return $this->content->getFieldsByLanguage($languageCode);
    }

    /**
     * This method returns the field for a given field definition identifier and language.
     *
     * If not set the initialLanguage of the content version is used.
     *
     * @param string $fieldDefIdentifier
     * @param string|null $languageCode
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Field|null A {@link Field} or null if nothing is found
     */
    public function getField(string $fieldDefIdentifier, ?string $languageCode = null): ?Field
    {
        return $this->content->getField($fieldDefIdentifier, $languageCode);
    }

    /**
     * Function where list of properties are returned.
     *
     * Override to add dynamic properties
     *
     * @uses \parent::getProperties()
     *
     * @param array $dynamicProperties
     *
     * @return array
     */
    protected function getProperties($dynamicProperties = ['id', 'contentInfo', 'versionInfo', 'fields'])
    {
        return parent::getProperties($dynamicProperties);
    }

    /**
     * Magic getter for retrieving convenience properties.
     *
     * @param string $property The name of the property to retrieve
     *
     * @return mixed
     */
    public function __get($property)
    {
        switch ($property) {
            case 'contentInfo':
                return $this->getVersionInfo()->getContentInfo();

            case 'id':
                return $this->getVersionInfo()->getContentInfo()->id;

            case 'versionInfo':
                return $this->getVersionInfo();

            case 'fields':
                return $this->getFields();

            case 'thumbnail':
                return $this->getThumbnail();

            case 'content':
                // trigger error for this, but for BC let it pass on to normal __get lookup for now
                @trigger_error(
                    sprintf('%s is an internal property, usage is deprecated as of 6.10. User itself exposes everything needed.', $property),
                    E_USER_DEPRECATED
                );
        }

        return parent::__get($property);
    }

    /**
     * Magic isset for signaling existence of convenience properties.
     *
     * @param string $property
     *
     * @return bool
     */
    public function __isset($property)
    {
        if ($property === 'contentInfo') {
            return true;
        }

        if ($property === 'id') {
            return true;
        }

        if ($property === 'versionInfo') {
            return true;
        }

        if ($property === 'thumbnail') {
            return true;
        }

        if ($property === 'fields') {
            return true;
        }

        return parent::__isset($property);
    }

    public function getThumbnail(): ?Thumbnail
    {
        return $this->content->getThumbnail();
    }
}

class_alias(User::class, 'eZ\Publish\Core\Repository\Values\User\User');
