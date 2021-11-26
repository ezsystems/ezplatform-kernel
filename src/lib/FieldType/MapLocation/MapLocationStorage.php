<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\MapLocation;

use Ibexa\Contracts\Core\FieldType\GatewayBasedStorage;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;

/**
 * Storage for the MapLocation field type.
 */
class MapLocationStorage extends GatewayBasedStorage
{
    /** @var \Ibexa\Core\FieldType\MapLocation\MapLocationStorage\Gateway */
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
        return $this->gateway->storeFieldData($versionInfo, $field);
    }

    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        $this->gateway->getFieldData($versionInfo, $field);
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
        $this->gateway->deleteFieldData($versionInfo, $fieldIds);
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
        return is_array($field->value->externalData) ? $field->value->externalData['address'] : null;
    }
}

class_alias(MapLocationStorage::class, 'eZ\Publish\Core\FieldType\MapLocation\MapLocationStorage');
