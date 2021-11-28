<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Keyword;

use Ibexa\Contracts\Core\FieldType\GatewayBasedStorage;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;

/**
 * Converter for Keyword field type external storage.
 *
 * The keyword storage ships a list (array) of keywords in
 * $field->value->externalData. $field->value->data is simply empty, because no
 * internal data is store.
 */
class KeywordStorage extends GatewayBasedStorage
{
    /** @var \Ibexa\Core\FieldType\Keyword\KeywordStorage\Gateway */
    protected $gateway;

    /**
     * @see \Ibexa\Contracts\Core\FieldType\FieldStorage
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     * @param array $context
     *
     * @return mixed
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        $contentTypeId = $this->gateway->getContentTypeId($field);

        return $this->gateway->storeFieldData($field, $contentTypeId);
    }

    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        // @todo: This should already retrieve the ContentType ID
        return $this->gateway->getFieldData($field);
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
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
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     * @param array $context
     *
     * @return \Ibexa\Contracts\Core\Search\Field[]|null
     */
    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
        return null;
    }
}

class_alias(KeywordStorage::class, 'eZ\Publish\Core\FieldType\Keyword\KeywordStorage');
