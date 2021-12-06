<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Imagine\Filter\Loader;

use Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleDownOnlyFilterLoader;
use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use PHPUnit\Framework\TestCase;

class ScaleDownOnlyFilterLoaderTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $innerLoader;

    /** @var \Ibexa\Bundle\Core\Imagine\Filter\Loader\ScaleDownOnlyFilterLoader */
    private $loader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->innerLoader = $this->createMock(LoaderInterface::class);
        $this->loader = new ScaleDownOnlyFilterLoader();
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

    public function testLoad()
    {
        $options = [123, 456];
        $image = $this->createMock(ImageInterface::class);
        $this->innerLoader
            ->expects($this->once())
            ->method('load')
            ->with($image, $this->equalTo(['size' => $options, 'mode' => 'inset']))
            ->will($this->returnValue($image));

        $this->assertSame($image, $this->loader->load($image, $options));
    }
}

class_alias(ScaleDownOnlyFilterLoaderTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Imagine\Filter\Loader\ScaleDownOnlyFilterLoaderTest');
