<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\MapLocation\MapLocationStorage;

use Ibexa\Contracts\Core\FieldType\StorageGateway;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;

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
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     *
     * @return bool If restoring of the internal field data is required
     */
    abstract public function storeFieldData(VersionInfo $versionInfo, Field $field);

    /**
     * Sets the loaded field data into $field->externalData.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     *
     * @return array
     */
    abstract public function getFieldData(VersionInfo $versionInfo, Field $field);

    /**
     * Deletes the data for all given $fieldIds.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param array $fieldIds
     */
    abstract public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds);
}

class_alias(Gateway::class, 'eZ\Publish\Core\FieldType\MapLocation\MapLocationStorage\Gateway');
