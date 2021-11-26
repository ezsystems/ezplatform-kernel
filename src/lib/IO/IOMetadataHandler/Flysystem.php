<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\IO\IOMetadataHandler;

use DateTime;
use Ibexa\Contracts\Core\IO\BinaryFile as SPIBinaryFile;
use Ibexa\Contracts\Core\IO\BinaryFileCreateStruct as SPIBinaryFileCreateStruct;
use Ibexa\Core\IO\Exception\BinaryFileNotFoundException;
use Ibexa\Core\IO\IOMetadataHandler;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;

class Flysystem implements IOMetadataHandler
{
    /** @var \League\Flysystem\FilesystemInterface */
    private $filesystem;

    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Only reads & return metadata, since the binarydata handler took care of creating the file already.
     *
     * @throws \Ibexa\Core\IO\Exception\BinaryFileNotFoundException
     */
    public function create(SPIBinaryFileCreateStruct $spiBinaryFileCreateStruct)
    {
        return $this->load($spiBinaryFileCreateStruct->id);
    }

    /**
     * Does really nothing, the binary data handler takes care of it.
     *
     * @param $spiBinaryFileId
     */
    public function delete($spiBinaryFileId)
    {
    }

    public function load($spiBinaryFileId)
    {
        try {
            $info = $this->filesystem->getMetadata($spiBinaryFileId);
        } catch (FileNotFoundException $e) {
            throw new BinaryFileNotFoundException($spiBinaryFileId);
        }

        $spiBinaryFile = new SPIBinaryFile();
        $spiBinaryFile->id = $spiBinaryFileId;
        $spiBinaryFile->size = $info['size'];

        if (isset($info['timestamp'])) {
            $spiBinaryFile->mtime = new DateTime('@' . $info['timestamp']);
        }

        return $spiBinaryFile;
    }

    public function exists($spiBinaryFileId)
    {
        return $this->filesystem->has($spiBinaryFileId);
    }

    public function getMimeType($spiBinaryFileId)
    {
        return $this->filesystem->getMimetype($spiBinaryFileId);
    }

    /**
     * Does nothing, as the binarydata handler takes care of it.
     */
    public function deleteDirectory($spiPath)
    {
    }
}

class_alias(Flysystem::class, 'eZ\Publish\Core\IO\IOMetadataHandler\Flysystem');
