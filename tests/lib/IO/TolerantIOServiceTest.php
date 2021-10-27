<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\IO;

use Ibexa\Core\IO\TolerantIOService;
use Ibexa\Core\IO\Values\MissingBinaryFile;

/**
 * @covers \Ibexa\Core\IO\IOService
 */
class TolerantIOServiceTest extends IOServiceTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->IOService = new TolerantIOService(
            $this->metadataHandlerMock,
            $this->binarydataHandlerMock,
            $this->mimeTypeDetectorMock,
            ['prefix' => self::PREFIX]
        );
    }

    public function testLoadBinaryFileNotFound()
    {
        $binaryFile = parent::loadBinaryFileNotFound();

        self::assertEquals(
            new MissingBinaryFile(['id' => 'id.ext']),
            $binaryFile
        );
    }

    public function testCreateMissingBinaryFile()
    {
        $id = 'id.ext';
        $prefixedUri = $this->getPrefixedUri($id);

        $this->binarydataHandlerMock
            ->expects($this->once())
            ->method('getUri')
            ->with($prefixedUri)
            ->will($this->returnValue("/$prefixedUri"));

        $binaryFile = parent::loadBinaryFileNotFound();
        self::assertEquals(
            new MissingBinaryFile(['id' => 'id.ext', 'uri' => "/$prefixedUri"]),
            $binaryFile
        );
    }

    /**
     * Overridden to change the expected exception (none).
     */
    public function testDeleteBinaryFileNotFound()
    {
        $this->deleteBinaryFileNotFound();
    }

    public function testLoadBinaryFileByUriNotFound()
    {
        self::assertEquals(
            new MissingBinaryFile(['id' => 'my/path.png']),
            $this->loadBinaryFileByUriNotFound()
        );
    }
}

class_alias(TolerantIOServiceTest::class, 'eZ\Publish\Core\IO\Tests\TolerantIOServiceTest');
