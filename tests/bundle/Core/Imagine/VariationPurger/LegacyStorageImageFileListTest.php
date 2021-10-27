<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\Imagine\VariationPurger;

use Ibexa\Bundle\Core\Imagine\VariationPurger\ImageFileRowReader;
use Ibexa\Bundle\Core\Imagine\VariationPurger\LegacyStorageImageFileList;
use Ibexa\Core\IO\IOConfigProvider;
use Ibexa\Core\MVC\ConfigResolverInterface;
use PHPUnit\Framework\TestCase;

class LegacyStorageImageFileListTest extends TestCase
{
    /** @var \Ibexa\Bundle\Core\Imagine\VariationPurger\ImageFileRowReader|\PHPUnit\Framework\MockObject\MockObject */
    protected $rowReaderMock;

    /** @var \Ibexa\Bundle\Core\Imagine\VariationPurger\LegacyStorageImageFileList */
    protected $fileList;

    /** @var \Ibexa\Core\IO\IOConfigProvider|\PHPUnit\Framework\MockObject\MockObject */
    private $ioConfigResolverMock;

    /** @var \Ibexa\Core\MVC\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $configResolverMock;

    protected function setUp(): void
    {
        $this->rowReaderMock = $this->createMock(ImageFileRowReader::class);
        $this->ioConfigResolverMock = $this->createMock(IOConfigProvider::class);
        $this->ioConfigResolverMock
            ->method('getLegacyUrlPrefix')
            ->willReturn('var/ezdemo_site/storage');
        $this->configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $this->configResolverMock
            ->method('getParameter')
            ->with('image.published_images_dir')
            ->willReturn('images');

        $this->fileList = new LegacyStorageImageFileList(
            $this->rowReaderMock,
            $this->ioConfigResolverMock,
            $this->configResolverMock
        );
    }

    public function testIterator()
    {
        $expected = [
            'path/to/1st/image.jpg',
            'path/to/2nd/image.jpg',
        ];
        $this->configureRowReaderMock($expected);

        foreach ($this->fileList as $index => $file) {
            self::assertEquals($expected[$index], $file);
        }
    }

    /**
     * Tests that the iterator transforms the ezimagefile value into a binaryfile id.
     */
    public function testImageIdTransformation()
    {
        $this->configureRowReaderMock(['var/ezdemo_site/storage/images/path/to/1st/image.jpg']);
        foreach ($this->fileList as $file) {
            self::assertEquals('path/to/1st/image.jpg', $file);
        }
    }

    private function configureRowReaderMock(array $fileList)
    {
        $mockInvocator = $this->rowReaderMock->expects($this->any())->method('getRow');
        call_user_func_array([$mockInvocator, 'willReturnOnConsecutiveCalls'], $fileList);

        $this->rowReaderMock->expects($this->any())->method('getCount')->willReturn(count($fileList));
    }
}

class_alias(LegacyStorageImageFileListTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Imagine\VariationPurger\LegacyStorageImageFileListTest');
