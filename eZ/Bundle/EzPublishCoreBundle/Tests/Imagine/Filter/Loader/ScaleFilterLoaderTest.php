<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\Tests\Imagine\Filter\Loader;

use eZ\Bundle\EzPublishCoreBundle\Imagine\Filter\Loader\ScaleFilterLoader;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use PHPUnit\Framework\TestCase;

class ScaleFilterLoaderTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $innerLoader;

    /** @var \eZ\Bundle\EzPublishCoreBundle\Imagine\Filter\Loader\ScaleFilterLoader */
    private $loader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->innerLoader = $this->createMock(LoaderInterface::class);
        $this->loader = new ScaleFilterLoader();
        $this->loader->setInnerLoader($this->innerLoader);
    }

    /**
     * @dataProvider loadInvalidProvider
     */
    public function testLoadInvalidOptions(array $options)
    {
        $this->expectException(\Imagine\Exception\InvalidArgumentException::class);

        $this->loader->load($this->createMock(ImageInterface::class), $options);
    }

    public function loadInvalidProvider()
    {
        return [
            [[]],
            [[123]],
            [['foo' => 'bar']],
        ];
    }

    public function testLoadHeighten()
    {
        $width = 900;
        $height = 400;
        $origWidth = 770;
        $origHeight = 377;
        $box = new Box($origWidth, $origHeight);

        $image = $this->createMock(ImageInterface::class);
        $image
            ->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue($box));

        $this->innerLoader
            ->expects($this->once())
            ->method('load')
            ->with($image, $this->equalTo(['heighten' => $height]))
            ->will($this->returnValue($image));

        $this->assertSame($image, $this->loader->load($image, [$width, $height]));
    }

    public function testLoadWiden()
    {
        $width = 900;
        $height = 600;
        $origWidth = 770;
        $origHeight = 377;
        $box = new Box($origWidth, $origHeight);

        $image = $this->createMock(ImageInterface::class);
        $image
            ->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue($box));

        $this->innerLoader
            ->expects($this->once())
            ->method('load')
            ->with($image, $this->equalTo(['widen' => $width]))
            ->will($this->returnValue($image));

        $this->assertSame($image, $this->loader->load($image, [$width, $height]));
    }
}
