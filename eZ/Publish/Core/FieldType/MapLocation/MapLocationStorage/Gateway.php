<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\FieldType\MapLocation\MapLocationStorage;

use eZ\Publish\SPI\FieldType\StorageGateway;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

/**
 * MapLocation Field Type external storage gateway.
 */
abstract class Gateway extends StorageGateway
{
    /**
     * Stores the data stored in the given $field.
     *
     * Potentially rewrites data in $field and returns true, if the $field
     * needs to be updated in the database.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     *
     * @return bool If restoring of the internal field data is required
     */
    abstract public function storeFieldData(VersionInfo $versionInfo, Field $field);

    /**
     * Sets the loaded field data into $field->externalData.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     *
     * @return array
     */
    abstract public function getFieldData(VersionInfo $versionInfo, Field $field);

    /**
     * Deletes the data for all given $fieldIds.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param array $fieldIds
     */
    abstract public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds);
}
