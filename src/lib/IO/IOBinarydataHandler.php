<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\IO;

use Ibexa\Contracts\Core\IO\BinaryFileCreateStruct;

/**
 * Provides reading & writing of files binary data.
 */
interface IOBinarydataHandler
{
    /**
     * Creates a new file with data from $binaryFileCreateStruct.
     *
     * @param \Ibexa\Contracts\Core\IO\BinaryFileCreateStruct $binaryFileCreateStruct
     *
     * @throws \RuntimeException if an error occured creating the file
     */
    public function create(BinaryFileCreateStruct $binaryFileCreateStruct);

    /**
     * Deletes the file $path.
     *
     * @param string $spiBinaryFileId
     *
     * @throws \Ibexa\Core\IO\Exception\BinaryFileNotFoundException If the file is not found
     */
    public function delete($spiBinaryFileId);

    /**
     * Returns the binary content from $path.
     *
     * @param $spiBinaryFileId
     *
     * @throws \Ibexa\Core\IO\Exception\BinaryFileNotFoundException If $path is not found
     *
     * @return string
     */
    public function getContents($spiBinaryFileId);

    /**
     * Returns a read-only, binary file resource to $path.
     *
     * @param string $spiBinaryFileId
     *
     * @return resource A read-only binary resource to $path
     */
    public function getResource($spiBinaryFileId);

    /**
     * Returns the public URI for $path.
     *
     * @param string $spiBinaryFileId
     *
     * @return string
     */
    public function getUri($spiBinaryFileId);

    /**
     * Returns the id in $binaryFileUri.
     *
     * @param string $binaryFileUri
     *
     * @return string
     */
    public function getIdFromUri($binaryFileUri);

    /**
     * Deletes the directory $spiPath and all of its contents.
     *
     * @param string $spiPath
     */
    public function deleteDirectory($spiPath);
}

class_alias(IOBinarydataHandler::class, 'eZ\Publish\Core\IO\IOBinarydataHandler');
