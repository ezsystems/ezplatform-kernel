<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\FieldType\Tests\Integration\BinaryBase\BinaryBaseStorage;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\FieldType\BinaryBase\BinaryBaseStorage;
use eZ\Publish\Core\FieldType\BinaryBase\BinaryBaseStorage\Gateway;
use eZ\Publish\Core\FieldType\BinaryFile\BinaryFileStorage\Gateway\DoctrineStorage;
use eZ\Publish\Core\FieldType\Tests\Integration\BaseCoreFieldTypeIntegrationTest;
use eZ\Publish\Core\FieldType\Validator\FileExtensionBlackListValidator;
use eZ\Publish\Core\IO\IOServiceInterface;
use eZ\Publish\Core\IO\Values\BinaryFile;
use eZ\Publish\Core\IO\Values\BinaryFileCreateStruct;
use eZ\Publish\SPI\FieldType\BinaryBase\PathGenerator;
use eZ\Publish\SPI\IO\MimeTypeDetector;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

class BinaryBaseStorageTest extends BaseCoreFieldTypeIntegrationTest
{
    /** @var \eZ\Publish\Core\FieldType\BinaryBase\BinaryBaseStorage\Gateway|\PHPUnit\Framework\MockObject\MockObject */
    protected $gateway;

    /** @var \eZ\Publish\SPI\FieldType\BinaryBase\PathGenerator|\PHPUnit\Framework\MockObject\MockObject */
    protected $pathGeneratorMock;

    /** @var \eZ\Publish\Core\IO\IOServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $ioServiceMock;

    /** @var \eZ\Publish\Core\FieldType\BinaryBase\BinaryBaseStorage|\PHPUnit\Framework\MockObject\MockObject */
    protected $storage;

    /** @var \eZ\Publish\Core\FieldType\Validator\FileExtensionBlackListValidator&\PHPUnit\Framework\MockObject\MockObject */
    protected $fileExtensionBlackListValidatorMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = $this->getStorageGateway();
        $this->pathGeneratorMock = $this->createMock(PathGenerator::class);
        $this->ioServiceMock = $this->createMock(IOServiceInterface::class);
        $this->fileExtensionBlackListValidatorMock = $this->createMock(
            FileExtensionBlackListValidator::class
        );
        $this->storage = $this->getMockBuilder(BinaryBaseStorage::class)
            ->onlyMethods([])
            ->setConstructorArgs(
                [
                    $this->gateway,
                    $this->ioServiceMock,
                    $this->pathGeneratorMock,
                    $this->createMock(MimeTypeDetector::class),
                    $this->fileExtensionBlackListValidatorMock,
                ]
            )
            ->getMock();
    }

    protected function getContext(): array
    {
        return ['context'];
    }

    public function testHasFieldData(): void
    {
        self::assertTrue($this->storage->hasFieldData());
    }

    /**
     * @dataProvider providerOfFieldData
     */
    public function testStoreFieldData(VersionInfo $versionInfo, Field $field): void
    {
        $binaryFileCreateStruct = new BinaryFileCreateStruct([
            'id' => 'qwerty12345',
            'size' => '372949',
            'mimeType' => 'image/jpeg',
        ]);

        $this->ioServiceMock
            ->expects(self::once())
            ->method('newBinaryCreateStructFromLocalFile')
            ->will($this->returnValue($binaryFileCreateStruct));

        $this->pathGeneratorMock
            ->expects(self::once())
            ->method('getStoragePathForField')
            ->with($field, $versionInfo)
            ->willReturn('image/qwerty12345.jpg');

        $this->ioServiceMock
            ->expects(self::once())
            ->method('createBinaryFile')
            ->with($binaryFileCreateStruct)
            ->willReturn(new BinaryFile());

        $this->storage->storeFieldData($versionInfo, $field, $this->getContext());

        $this->expectNotToPerformAssertions();
    }

    /**
     * @depends testStoreFieldData
     *
     * @dataProvider providerOfFieldData
     */
    public function testCopyLegacyField(VersionInfo $versionInfo, Field $originalField): void
    {
        $field = clone $originalField;
        $field->id = 124;
        $field->versionNo = 2;
        $field->value = new FieldValue([
            'externalData' => [
                'fileName' => '123.jpg',
                'downloadCount' => 0,
                'mimeType' => null,
                'uri' => null,
            ],
        ]);

        $flag = $this->storage->copyLegacyField($versionInfo, $field, $originalField, $this->getContext());

        self::assertFalse($flag);
    }

    public function providerOfFieldData(): array
    {
        $field = new Field();
        $field->id = 124;
        $field->fieldDefinitionId = 231;
        $field->type = 'ezbinaryfile';
        $field->versionNo = 1;
        $field->value = new FieldValue([
            'externalData' => [
                'id' => 'image/aaac753a26e11f363cd8c14d824d162a.jpg',
                'path' => '/tmp/phpR4tNSV',
                'inputUri' => '/tmp/phpR4tNSV',
                'fileName' => '123.jpg',
                'fileSize' => '12345',
                'mimeType' => 'image/jpeg',
                'uri' => '/admin/content/download/75/320?version=1',
                'downloadCount' => 0,
            ],
        ]);

        $versionInfo = new VersionInfo([
            'contentInfo' => new ContentInfo([
                'id' => 235,
                'contentTypeId' => 24,
            ]),
            'versionNo' => 1,
        ]);

        return [
            [$versionInfo, $field],
        ];
    }

    protected function getStorageGateway(): Gateway
    {
        return new DoctrineStorage($this->getDatabaseConnection());
    }
}
