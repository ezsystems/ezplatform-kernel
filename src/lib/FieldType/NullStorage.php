<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType;

use Ibexa\Contracts\Core\FieldType\FieldStorage;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;

/**
 * Description of NullStorage.
 */
class NullStorage implements FieldStorage
{
    /**
     * @see \Ibexa\Contracts\Core\FieldType\FieldStorage::storeFieldData()
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        return false;
    }

    /**
     * @see \Ibexa\Contracts\Core\FieldType\FieldStorage::getFieldData()
     */
    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        return;
    }

    /**
     * @see \Ibexa\Contracts\Core\FieldType\FieldStorage::deleteFieldData()
     */
    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context)
    {
        return true;
    }

    /**
     * @see \Ibexa\Contracts\Core\FieldType\FieldStorage::hasFieldData()
     *
     * @return bool
     */
    public function hasFieldData()
    {
        return false;
    }

    /**
     * @see \Ibexa\Contracts\Core\FieldType\FieldStorage::getIndexData()
     */
    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
        return false;
    }

    /**
     * This method is used exclusively by Legacy Storage to copy external data of existing field in main language to
     * the untranslatable field not passed in create or update struct, but created implicitly in storage layer.
     *
     * By default, the method falls back to the {@link \Ibexa\Contracts\Core\FieldType\FieldStorage::storeFieldData()}.
     * External storages implement this method as needed.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $originalField
     * @param array $context
     *
     * @return bool|null Same as {@link \Ibexa\Contracts\Core\FieldType\FieldStorage::storeFieldData()}.
     */
    public function copyLegacyField(VersionInfo $versionInfo, Field $field, Field $originalField, array $context)
    {
        return;
    }
}

class_alias(NullStorage::class, 'eZ\Publish\Core\FieldType\NullStorage');
