<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\IO\MimeTypeDetector;

use Ibexa\Core\IO\MimeTypeDetector\FileInfo as MimeTypeDetector;
use PHPUnit\Framework\TestCase;

class FileInfoTest extends TestCase
{
    /** @var \Ibexa\Core\IO\MimeTypeDetector\FileInfo */
    protected $mimeTypeDetector;

    protected function setUp(): void
    {
        $this->mimeTypeDetector = new MimeTypeDetector();
    }

    protected function getFixture()
    {
        return __DIR__ . '/../../_fixtures/squirrel-developers.jpg';
    }

    public function testGetFromPath()
    {
        self::assertEquals(
            $this->mimeTypeDetector->getFromPath(
                $this->getFixture()
            ),
            'image/jpeg'
        );
    }

    public function testGetFromBuffer()
    {
        self::assertEquals(
            $this->mimeTypeDetector->getFromBuffer(
                file_get_contents($this->getFixture())
            ),
            'image/jpeg'
        );
    }
}

class_alias(FileInfoTest::class, 'eZ\Publish\Core\IO\Tests\MimeTypeDetector\FileInfoTest');
