<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishIOBundle\Migration\FileMigrator;

use eZ\Bundle\EzPublishIOBundle\Migration\FileMigratorInterface;
use eZ\Bundle\EzPublishIOBundle\Migration\MigrationHandler;
use eZ\Publish\Core\IO\Exception\BinaryFileNotFoundException;
use eZ\Publish\SPI\IO\BinaryFile;
use eZ\Publish\SPI\IO\BinaryFileCreateStruct;

final class FileMigrator extends MigrationHandler implements FileMigratorInterface
{
    public function migrateFile(BinaryFile $binaryFile)
    {
        if (!$this->migrateBinaryFile($binaryFile) || !$this->migrateMetadata($binaryFile)) {
            return false;
        }

        $this->logInfo("Successfully migrated: '{$binaryFile->id}'");

        return true;
    }

    private function migrateBinaryFile(BinaryFile $binaryFile): bool
    {
        if (get_class($this->fromBinarydataHandler) === get_class($this->toBinarydataHandler)) {
            return true;
        }

        try {
            $binaryFileResource = $this->fromBinarydataHandler->getResource($binaryFile->id);
        } catch (BinaryFileNotFoundException $e) {
            $this->logError("Cannot load binary data for: '{$binaryFile->id}'. Error: " . $e->getMessage());

            return false;
        }

        $binaryFileCreateStruct = new BinaryFileCreateStruct();
        $binaryFileCreateStruct->id = $binaryFile->id;
        $binaryFileCreateStruct->setInputStream($binaryFileResource);

        try {
            $this->toBinarydataHandler->create($binaryFileCreateStruct);
        } catch (\RuntimeException $e) {
            $this->logError("Cannot migrate binary data for: '{$binaryFile->id}'. Error: " . $e->getMessage());

            return false;
        }

        return true;
    }

    private function migrateMetadata(BinaryFile $binaryFile): bool
    {
        if (get_class($this->fromMetadataHandler) === get_class($this->toMetadataHandler)) {
            return true;
        }

        $metadataCreateStruct = new BinaryFileCreateStruct();
        $metadataCreateStruct->id = $binaryFile->id;
        $metadataCreateStruct->size = $binaryFile->size;
        $metadataCreateStruct->mtime = $binaryFile->mtime;
        $metadataCreateStruct->mimeType = $this->fromMetadataHandler->getMimeType($binaryFile->id);

        try {
            $this->toMetadataHandler->create($metadataCreateStruct);
        } catch (\RuntimeException $e) {
            $this->logError("Cannot migrate metadata for: '{$binaryFile->id}'. Error: " . $e->getMessage() . $e->getPrevious()->getMessage());

            return false;
        }

        return true;
    }
}
