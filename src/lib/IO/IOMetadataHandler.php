<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\IO;

use Ibexa\Contracts\Core\IO\BinaryFileCreateStruct;

/**
 * Provides reading & writing of files meta data (size, modification time...).
 */
interface IOMetadataHandler
{
    /**
     * Stores the file from $binaryFileCreateStruct.
     *
     * @param \Ibexa\Contracts\Core\IO\BinaryFileCreateStruct $spiBinaryFileCreateStruct
     *
     * @return \Ibexa\Contracts\Core\IO\BinaryFile
     *
     * @throws \RuntimeException if an error occured creating the file
     */
    public function create(BinaryFileCreateStruct $spiBinaryFileCreateStruct);

    /**
     * Deletes file $spiBinaryFileId.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If $spiBinaryFileId is not found
     *
     * @param string $spiBinaryFileId
     */
    public function delete($spiBinaryFileId);

    /**
     * Loads and returns metadata for $spiBinaryFileId.
     *
     * @param string $spiBinaryFileId
     *
     * @return \Ibexa\Contracts\Core\IO\BinaryFile
     */
    public function load($spiBinaryFileId);

    /**
     * Checks if a file $spiBinaryFileId exists.
     *
     * @param string $spiBinaryFileId
     *
     * @return bool
     */
    public function exists($spiBinaryFileId);

    /**
     * Returns the file's mimetype. Example: text/plain.
     *
     * @param $spiBinaryFileId
     *
     * @return string
     */
    public function getMimeType($spiBinaryFileId);

    public function deleteDirectory($spiPath);
}

class_alias(IOMetadataHandler::class, 'eZ\Publish\Core\IO\IOMetadataHandler');
