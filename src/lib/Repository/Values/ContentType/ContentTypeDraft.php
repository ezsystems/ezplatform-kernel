<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Repository\Values\ContentType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft as APIContentTypeDraft;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCollection as APIFieldDefinitionCollection;
use Ibexa\Core\Repository\Values\MultiLanguageTrait;

/**
 * This class represents a draft of a content type.
 *
 * @internal Meant for internal use by Repository, type hint against API object instead.
 */
class ContentTypeDraft extends APIContentTypeDraft
{
    use MultiLanguageTrait;

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
    protected function getProperties($dynamicProperties = ['contentTypeGroups', 'fieldDefinitions'])
    {
        return parent::getProperties($dynamicProperties);
    }

    /**
     * Magic getter for routing get calls to innerContentType.
     *
     * @param string $property The name of the property to retrieve
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->innerContentType->$property;
    }

    /**
     * Magic set for routing set calls to innerContentType.
     *
     * @param string $property
     * @param mixed $propertyValue
     */
    public function __set($property, $propertyValue)
    {
        $this->innerContentType->$property = $propertyValue;
    }

    /**
     * Magic isset for routing isset calls to innerContentType.
     *
     * @param string $property
     *
     * @return bool
     */
    public function __isset($property)
    {
        return $this->innerContentType->__isset($property);
    }

    /**
     * Holds internal content type object.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
     *
     * @todo document
     */
    protected $innerContentType;

    /**
     * {@inheritdoc}
     */
    public function getNames()
    {
        return $this->innerContentType->getNames();
    }

    /**
     * {@inheritdoc}
     */
    public function getName($languageCode = null)
    {
        return $this->innerContentType->getName($languageCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescriptions()
    {
        return $this->innerContentType->getDescriptions();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($languageCode = null)
    {
        return $this->innerContentType->getDescription($languageCode);
    }

    /**
     * This method returns the content type groups this content type is assigned to.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup[]
     */
    public function getContentTypeGroups()
    {
        return $this->innerContentType->contentTypeGroups;
    }

    /**
     * This method returns the content type field definitions from this type.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition[]
     */
    public function getFieldDefinitions(): APIFieldDefinitionCollection
    {
        return $this->innerContentType->getFieldDefinitions();
    }

    /**
     * This method returns the field definition for the given identifier.
     *
     * @param string $fieldDefinitionIdentifier
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition
     */
    public function getFieldDefinition($fieldDefinitionIdentifier): ?FieldDefinition
    {
        return $this->innerContentType->getFieldDefinition($fieldDefinitionIdentifier);
    }

    public function hasFieldDefinition(string $fieldDefinitionIdentifier): bool
    {
        return $this->innerContentType->hasFieldDefinition($fieldDefinitionIdentifier);
    }
}

class_alias(ContentTypeDraft::class, 'eZ\Publish\Core\Repository\Values\ContentType\ContentTypeDraft');
