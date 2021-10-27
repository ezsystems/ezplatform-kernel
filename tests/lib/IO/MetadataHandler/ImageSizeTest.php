<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\IO\MetadataHandler;

use Ibexa\Core\IO\MetadataHandler\ImageSize as ImageSizeMetadataHandler;
use PHPUnit\Framework\TestCase;

/**
 * @group fieldType
 * @group ezimage
 */
class ImageSizeTest extends TestCase
{
    public function testExtract()
    {
        $metadataHandler = new ImageSizeMetadataHandler();
        $file = __DIR__ . '/ezplogo.png';
        self::assertEquals(
            ['width' => 189, 'height' => 200, 'mime' => 'image/png'],
            $metadataHandler->extract($file)
        );
    }
}

class_alias(ImageSizeTest::class, 'eZ\Publish\Core\IO\Tests\MetadataHandler\ImageSizeTest');
