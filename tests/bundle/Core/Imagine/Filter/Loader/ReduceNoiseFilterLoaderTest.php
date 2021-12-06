<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Imagine\Filter\Loader;

use Ibexa\Bundle\Core\Imagine\Filter\FilterInterface;
use Ibexa\Bundle\Core\Imagine\Filter\Loader\ReduceNoiseFilterLoader;
use Imagine\Image\ImageInterface;
use PHPUnit\Framework\TestCase;

class ReduceNoiseFilterLoaderTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $filter;

    /** @var \Ibexa\Bundle\Core\Imagine\Filter\Loader\ReduceNoiseFilterLoader */
    private $loader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = $this->createMock(FilterInterface::class);
        $this->loader = new ReduceNoiseFilterLoader($this->filter);
    }

    public function testLoadInvalidDriver()
    {
        $this->expectException(\Imagine\Exception\NotSupportedException::class);

        $this->loader->load($this->createMock(ImageInterface::class));
    }
}

class_alias(ReduceNoiseFilterLoaderTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Imagine\Filter\Loader\ReduceNoiseFilterLoaderTest');
