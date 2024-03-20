<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Image\ImageStorage;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Utils\DeprecationWarnerInterface;
use eZ\Publish\Core\FieldType\Image\AliasCleanerInterface;
use eZ\Publish\Core\FieldType\Image\ImageStorage;
use eZ\Publish\Core\FieldType\Image\ImageStorage\Gateway\DoctrineStorage;
use eZ\Publish\Core\FieldType\Image\PathGenerator;
use eZ\Publish\Core\FieldType\Tests\Integration\BaseCoreFieldTypeIntegrationTest;
use eZ\Publish\Core\FieldType\Validator\FileExtensionBlackListValidator;
use eZ\Publish\Core\IO\FilePathNormalizerInterface;
use eZ\Publish\Core\IO\IOServiceInterface;
use eZ\Publish\Core\IO\MetadataHandler;
use eZ\Publish\Core\IO\UrlRedecoratorInterface;
use eZ\Publish\Core\IO\Values\BinaryFile;
use eZ\Publish\Core\IO\Values\BinaryFileCreateStruct;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

final class ImageStorageTest extends BaseCoreFieldTypeIntegrationTest
{
    /** @var \eZ\Publish\Core\FieldType\Image\ImageStorage\Gateway */
    private $gateway;

    /** @var \eZ\Publish\Core\IO\UrlRedecoratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $redecorator;

    /** @var \eZ\Publish\Core\IO\MetadataHandler|\PHPUnit\Framework\MockObject\MockObject */
    private $imageSizeMetadataHandler;

    /** @var \eZ\Publish\Core\FieldType\Image\PathGenerator|\PHPUnit\Framework\MockObject\MockObject */
    private $pathGenerator;

    /** @var \eZ\Publish\Core\Base\Utils\DeprecationWarnerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $deprecationWarner;

    /** @var \eZ\Publish\Core\FieldType\Image\AliasCleanerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $aliasCleaner;

    /** @var \eZ\Publish\Core\IO\FilePathNormalizerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $filePathNormalizer;

    /** @var \eZ\Publish\Core\IO\IOServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $ioService;

    /** @var \eZ\Publish\Core\FieldType\Image\ImageStorage */
    private $storage;

    /** @var \eZ\Publish\Core\FieldType\Validator\FileExtensionBlackListValidator&\PHPUnit\Framework\MockObject\MockObject */
    private $fileExtensionBlackListValidator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->redecorator = $this->createMock(UrlRedecoratorInterface::class);
        $this->gateway = new DoctrineStorage($this->redecorator, $this->getDatabaseConnection());
        $this->imageSizeMetadataHandler = $this->createMock(MetadataHandler::class);
        $this->pathGenerator = $this->createMock(PathGenerator::class);
        $this->deprecationWarner = $this->createMock(DeprecationWarnerInterface::class);
        $this->aliasCleaner = $this->createMock(AliasCleanerInterface::class);
        $this->filePathNormalizer = $this->createMock(FilePathNormalizerInterface::class);
        $this->ioService = $this->createMock(IOServiceInterface::class);
        $this->fileExtensionBlackListValidator = $this->createMock(FileExtensionBlackListValidator::class);
        $this->storage = new ImageStorage(
            $this->gateway,
            $this->ioService,
            $this->pathGenerator,
            $this->imageSizeMetadataHandler,
            $this->deprecationWarner,
            $this->aliasCleaner,
            $this->filePathNormalizer,
            $this->fileExtensionBlackListValidator
        );
    }

    public function testHasFieldData(): void
    {
        self::assertTrue($this->storage->hasFieldData());
    }

    /**
     * @return array<string, string>
     */
    private function getContext(): array
    {
        return ['identifier' => 'LegacyStorage'];
    }

    /**
     * @dataProvider providerOfFieldData
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\Core\IO\Exception\InvalidBinaryFileIdException
     */
    public function testStoreFieldDataDuringCreate(VersionInfo $versionInfo, Field $field): void
    {
        $binaryFile = $this->runCommonStoreFieldDataMocks($field);

        $this->redecorator
            ->expects(self::exactly(3))
            ->method('redecorateFromSource')
            ->with($binaryFile->uri)
            ->willReturn($binaryFile->uri);

        $this->storage->storeFieldData($versionInfo, $field, $this->getContext());

        self::assertSame(1, $this->gateway->countImageReferences($binaryFile->uri));
    }

    /**
     * @dataProvider providerOfFieldData
     *
     * @depends testStoreFieldDataDuringCreate
     */
    public function testStoreFieldDataDuringUpdate(VersionInfo $versionInfo, Field $field): void
    {
        $binaryFile = $this->runCommonStoreFieldDataMocks($field);

        $this->redecorator
            ->expects(self::exactly(2))
            ->method('redecorateFromSource')
            ->with($binaryFile->uri)
            ->willReturn($binaryFile->uri);

        $this->storage->storeFieldData($versionInfo, $field, $this->getContext());

        self::assertSame(1, $this->gateway->countImageReferences($binaryFile->uri));
    }

    /**
     * @dataProvider providerOfFieldData
     *
     * @depends testStoreFieldDataDuringUpdate
     */
    public function testStoreFieldDataDuringUpdateWithDifferentImage(VersionInfo $versionInfo, Field $field): void
    {
        $versionInfo->versionNo = 2;
        $field->versionNo = 2;

        $path = __DIR__ . '/image.jpg';
        $newFieldValue = new FieldValue([
            'externalData' => [
                'id' => null,
                'path' => $path,
                'inputUri' => $path,
                'fileName' => 'image2.jpg',
                'fileSize' => '12',
                'mimeType' => 'image/jpeg',
                'width' => null,
                'height' => null,
                'alternativeText' => null,
                'imageId' => null,
                'uri' => null,
                'additionalData' => [],
            ],
        ]);
        $field->value = $newFieldValue;

        $binaryFile = $this->runCommonStoreFieldDataMocks($field);

        $this->redecorator
            ->expects(self::exactly(3))
            ->method('redecorateFromSource')
            ->with($binaryFile->uri)
            ->willReturn($binaryFile->uri);

        $this->storage->storeFieldData($versionInfo, $field, $this->getContext());

        self::assertSame(1, $this->gateway->countImageReferences($binaryFile->uri));
    }

    private function runCommonStoreFieldDataMocks(Field $field): BinaryFile
    {
        $this->filePathNormalizer
            ->expects(self::once())
            ->method('normalizePath')
            ->willReturn($targetPath = '1/8/6/232-eng-GB/' . $field->value->externalData['fileName']);

        $this->ioService
            ->expects(self::once())
            ->method('newBinaryCreateStructFromLocalFile')
            ->with($field->value->externalData['inputUri'])
            ->willReturn($newBinaryFileCreateStruct = new BinaryFileCreateStruct());

        $this->ioService
            ->expects(self::once())
            ->method('createBinaryFile')
            ->with($newBinaryFileCreateStruct)
            ->willReturn($binaryFile = new BinaryFile(
                [
                    'id' => $targetPath,
                    'uri' => $targetPath,
                ]
            ));

        $this->ioService
            ->expects(self::once())
            ->method('getMimeType')
            ->with($binaryFile->id)
            ->willReturn('image/jpeg');

        return $binaryFile;
    }

    /**
     * @return iterable<array{
     *     eZ\Publish\SPI\Persistence\Content\VersionInfo,
     *     eZ\Publish\SPI\Persistence\Content\Field
     * }>
     */
    public function providerOfFieldData(): iterable
    {
        $path = __DIR__ . '/image.jpg';

        $field = new Field();
        $field->id = 125;
        $field->fieldDefinitionId = 232;
        $field->type = 'ezimage';
        $field->versionNo = 1;
        $field->value = new FieldValue([
            'externalData' => [
                'id' => null,
                'path' => $path,
                'inputUri' => $path,
                'fileName' => 'image.jpg',
                'fileSize' => '12345',
                'mimeType' => 'image/jpeg',
                'width' => null,
                'height' => null,
                'alternativeText' => null,
                'imageId' => null,
                'uri' => null,
                'additionalData' => [],
            ],
        ]);

        $versionInfo = new VersionInfo([
            'contentInfo' => new ContentInfo([
                'id' => 236,
                'contentTypeId' => 25,
            ]),
            'versionNo' => 1,
        ]);

        yield [$versionInfo, $field];
    }
}
