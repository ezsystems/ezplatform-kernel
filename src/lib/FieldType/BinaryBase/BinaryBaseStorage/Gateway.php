<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\BinaryBase\BinaryBaseStorage;

use Ibexa\Contracts\Core\FieldType\StorageGateway;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;

abstract class Gateway extends StorageGateway
{
    /**
     * Stores the file reference in $field for $versionNo.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     */
    abstract public function storeFileReference(VersionInfo $versionInfo, Field $field);

    /**
     * Returns the file reference data for the given $fieldId in $versionNo.
     *
     * @param mixed $fieldId
     * @param int $versionNo
     *
     * @return array|void
     */
    abstract public function getFileReferenceData($fieldId, $versionNo);

    /**
     * Removes all file references for the given $fieldIds.
     *
     * @param array $fieldIds
     * @param int $versionNo
     */
    abstract public function removeFileReferences(array $fieldIds, $versionNo);

    /**
     * Removes a specific file reference for $fieldId and $versionId.
     *
     * @param mixed $fieldId
     * @param int $versionNo
     */
    abstract public function removeFileReference($fieldId, $versionNo);

    /**
     * Returns a map of files referenced by the given $fieldIds.
     *
     * @param array $fieldIds
     * @param int $versionNo
     *
     * @return array
     */
    abstract public function getReferencedFiles(array $fieldIds, $versionNo);

    /**
     * Returns a map with the number of references each file from $files has.
     *
     * @param array $files
     *
     * @return array
     */
    abstract public function countFileReferences(array $files);
}

class_alias(Gateway::class, 'eZ\Publish\Core\FieldType\BinaryBase\BinaryBaseStorage\Gateway');
