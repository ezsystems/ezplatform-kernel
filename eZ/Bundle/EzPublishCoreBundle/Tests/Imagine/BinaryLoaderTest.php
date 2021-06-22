<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\Tests\Imagine;

use eZ\Bundle\EzPublishCoreBundle\Imagine\BinaryLoader;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\IO\Exception\InvalidBinaryFileIdException;
use eZ\Publish\Core\IO\IOServiceInterface;
use eZ\Publish\Core\IO\Values\BinaryFile;
use eZ\Publish\Core\IO\Values\MissingBinaryFile;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Model\Binary;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\MimeTypes;

class BinaryLoaderTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $ioService;

    /** @var BinaryLoader */
    private $binaryLoader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ioService = $this->createMock(IOServiceInterface::class);
        $this->binaryLoader = new BinaryLoader($this->ioService, new MimeTypes());
    }

    public function testFindNotFound()
    {
        $this->expectException(\Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException::class);

        $path = 'something.jpg';
        $this->ioService
            ->expects($this->once())
            ->method('loadBinaryFile')
            ->with($path)
            ->will($this->throwException(new NotFoundException('foo', 'bar')));

        $this->binaryLoader->find($path);
    }

    public function testFindMissing()
    {
        $this->expectException(\Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException::class);

        $path = 'something.jpg';
        $this->ioService
            ->expects($this->once())
            ->method('loadBinaryFile')
            ->with($path)
            ->will($this->returnValue(new MissingBinaryFile()));

        $this->binaryLoader->find($path);
    }

    public function testFindBadPathRoot()
    {
        $path = 'var/site/storage/images/1/2/3/123-name/name.png';
        $this->ioService
            ->expects($this->once())
            ->method('loadBinaryFile')
            ->with($path)
            ->will($this->throwException(new InvalidBinaryFileIdException($path)));

        try {
            $this->binaryLoader->find($path);
        } catch (NotLoadableException $e) {
            $this->assertStringContainsString(
                "Suggested value: '1/2/3/123-name/name.png'",
                $e->getMessage()
            );
        }
    }

    public function testFind(): void
    {
        $path = 'something.jpg';
        $mimeType = 'image/jpeg';
        $content = 'some content';
        $binaryFile = new BinaryFile(['id' => $path]);
        $this->ioService
            ->method('loadBinaryFile')
            ->with($path)
            ->willReturn($binaryFile);

        $this->ioService
            ->method('getFileContents')
            ->with($binaryFile)
            ->willReturn($content);

        $this->ioService
            ->method('getMimeType')
            ->with($binaryFile->id)
            ->willReturn($mimeType);

        $expected = new Binary($content, $mimeType, 'jpg');
        self::assertEquals($expected, $this->binaryLoader->find($path));
    }
}
