<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\FieldType\Tests\Image;

use eZ\Publish\Core\FieldType\Image\AliasCleanerInterface;
use eZ\Publish\Core\FieldType\Image\ImageStorage;
use eZ\Publish\Core\FieldType\Image\PathGenerator;
use eZ\Publish\Core\IO\IOServiceInterface;
use eZ\Publish\Core\IO\UrlRedecoratorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * @internal
 */
final class ImageStorageTest extends TestCase
{
    /** @var \eZ\Publish\Core\FieldType\Image\ImageStorage */
    private $imageStorage;

    /** @var \eZ\Publish\Core\IO\UrlRedecoratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $urlRedecoratorMock;

    public function getDataForTestExtractOriginalFilePathsFromXML(): iterable
    {
        yield 'Correct image XML' => [
            <<<XML
<?xml version="1.0" encoding="utf-8"?>
<ezimage serial_number="1" is_valid="1" filename="foo.png"
    suffix="png" basename="foo" dirpath="var/site/storage/images/3/9/1/0/193-1-eng-GB" url="var/site/storage/images/3/9/1/0/193-1-eng-GB/foo.png"
    original_filename="foo.png" mime_type="image/png" width="1487"
    height="1105" alternative_text="" alias_key="1293033771" timestamp="1596794011">
  <original attribute_id="193" attribute_version="1" attribute_language="eng-GB"/>
  <information Height="1105" Width="1487" IsColor="1"/>
</ezimage>
XML,
            '/var/site/storage/images/3/9/1/0/193-1-eng-GB/foo.png',
        ];

        yield 'No XML' => [
            '',
            null,
        ];

        yield 'Image XML without URI' => [
            <<<XML
<?xml version="1.0" encoding="utf-8"?>
<ezimage>
  <original attribute_id="193" attribute_version="1" attribute_language="eng-GB"/>
  <information Height="1105" Width="1487" IsColor="1"/>
</ezimage>
XML,
            null,
        ];
    }

    protected function setUp(): void
    {
        $this->imageStorage = new ImageStorage(
            $this->createMock(ImageStorage\Gateway::class),
            $this->createMock(IOServiceInterface::class),
            $this->createMock(PathGenerator::class),
            $this->createMock(AliasCleanerInterface::class),
            $this->getUrlRedecoratorMock(),
            new NullLogger()
        );
    }

    /**
     * @dataProvider getDataForTestExtractOriginalFilePathsFromXML
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function testExtractOriginalFilePathsFromXML(string $xml, ?string $expectedURI): void
    {
        $this
            ->getUrlRedecoratorMock()
            ->method('redecorateFromTarget')
            ->willReturnCallback(
                // AbsolutePrefix mock
                static function (string $url) {
                    return "/{$url}";
                }
            );

        self::assertEquals(
            $expectedURI,
            $this->imageStorage->extractOriginalFilePathFromXML($xml),
        );
    }

    /**
     * @return \eZ\Publish\Core\IO\UrlRedecoratorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getUrlRedecoratorMock(): UrlRedecoratorInterface
    {
        if (null === $this->urlRedecoratorMock) {
            $this->urlRedecoratorMock = $this->createMock(UrlRedecoratorInterface::class);
        }

        return $this->urlRedecoratorMock;
    }
}
