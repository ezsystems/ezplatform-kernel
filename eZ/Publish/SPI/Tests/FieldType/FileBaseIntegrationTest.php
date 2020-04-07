<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\SPI\Tests\FieldType;

use eZ\Publish\Core\IO\IOServiceInterface;

abstract class FileBaseIntegrationTest extends BaseIntegrationTest
{
    /** @var IOServiceInterface */
    protected $ioService;

    /**
     * @see EZP-23534
     */
    public function testLoadingContentWithMissingFileWorks()
    {
        $contentType = $this->createContentType();
        $content = $this->createContent($contentType, $this->getInitialValue());

        // delete the binary file object
        $this->deleteStoredFile($content);

        // try loading the content again. It should work even though the image isn't physically here
        $this->getCustomHandler()->contentHandler()->load($content->versionInfo->contentInfo->id, 1);
    }

    /**
     * Deletes the binary file stored in the field.
     *
     * @param $content
     *
     * @return mixed
     */
    protected function deleteStoredFile($content)
    {
        return $this->ioService->deleteBinaryFile(
            $this->ioService->loadBinaryFile($content->fields[1]->value->externalData['id'])
        );
    }

    /**
     * Returns prefix used by the IOService.
     *
     * @return string
     */
    abstract protected function getStoragePrefix();

    /**
     * Asserts that the IO File with uri $uri exists.
     *
     * @param string $uri
     */
    protected function assertIOUriExists($uri)
    {
        $this->assertFileExists(
            self::$tmpIoRootDir . '/' . $uri,
            "Stored file uri $uri does not exist"
        );
    }

    /**
     * Asserts that the IO File with id $id exists.
     *
     * @param string $id
     */
    protected function assertIOIdExists($id)
    {
        $path = $this->getPathFromId($id);
        $this->assertFileExists(
            $path,
            "Stored file $path does not exists"
        );
    }

    /**
     * Returns the physical path to the file with id $id.
     */
    protected function getPathFromId($id)
    {
        return $this->getStorageDir() . '/' . $this->getStoragePrefix() . '/' . $id;
    }

    protected function getStorageDir(): string
    {
        return self::$container->getParameter('io_root_dir');
    }

    protected function getFilesize($binaryFileId)
    {
        return filesize($this->getPathFromId($binaryFileId));
    }
}
