<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\FieldType\Keyword;

use eZ\Publish\SPI\FieldType\GatewayBasedStorage;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

/**
 * Converter for Keyword field type external storage.
 *
 * The keyword storage ships a list (array) of keywords in
 * $field->value->externalData. $field->value->data is simply empty, because no
 * internal data is store.
 */
class KeywordStorage extends GatewayBasedStorage
{
    /** @var \eZ\Publish\Core\FieldType\Keyword\KeywordStorage\Gateway */
    protected $gateway;

    /**
     * @see \eZ\Publish\SPI\FieldType\FieldStorage
     *
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     * @param array $context
     *
     * @return mixed
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        $contentTypeId = $this->gateway->getContentTypeId($field);

        return $this->gateway->storeFieldData($field, $contentTypeId);
    }

    /**
     * Populates $field value property based on the external data.
     * $field->value is a {@link eZ\Publish\SPI\Persistence\Content\FieldValue} object.
     * This value holds the data as a {@link eZ\Publish\Core\FieldType\Value} based object,
     * according to the field type (e.g. for TextLine, it will be a {@link eZ\Publish\Core\FieldType\TextLine\Value} object).
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     * @param array $context
     */
    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        // @todo: This should already retrieve the ContentType ID
        return $this->gateway->getFieldData($field);
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param array $fieldIds
     * @param array $context
     *
     * @return bool
     */
    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context)
    {
        foreach ($fieldIds as $fieldId) {
            $this->gateway->deleteFieldData($fieldId, $versionInfo->versionNo);
        }

        return true;
    }

    /**
     * Checks if field type has external data to deal with.
     *
     * @return bool
     */
    public function hasFieldData()
    {
        return true;
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     * @param array $context
     *
     * @return \eZ\Publish\SPI\Search\Field[]|null
     */
    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
        return null;
    }
}
