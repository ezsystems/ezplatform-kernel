<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Integration\BinaryBase\BinaryBaseStorage;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\FieldType\BinaryBase\BinaryBaseStorage;
use eZ\Publish\Core\FieldType\BinaryBase\BinaryBaseStorage\Gateway;
use eZ\Publish\Core\FieldType\BinaryFile\BinaryFileStorage\Gateway\DoctrineStorage;
use eZ\Publish\Core\IO\IOServiceInterface;
use eZ\Publish\Core\IO\Values\BinaryFile;
use eZ\Publish\Core\IO\Values\BinaryFileCreateStruct;
use eZ\Publish\Core\Persistence\Legacy\Tests\TestCase;
use eZ\Publish\SPI\FieldType\BinaryBase\PathGenerator;
use eZ\Publish\SPI\IO\MimeTypeDetector;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

class BinaryBaseStorageTest extends TestCase
{
    /** @var \eZ\Publish\Core\FieldType\BinaryBase\BinaryBaseStorage\Gateway|\PHPUnit\Framework\MockObject\MockObject */
    protected $gatewayMock;

    /** @var \eZ\Publish\SPI\FieldType\BinaryBase\PathGenerator|\PHPUnit\Framework\MockObject\MockObject */
    protected $pathGeneratorMock;

    /** @var \eZ\Publish\Core\IO\IOServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $ioServiceMock;

    protected function setUp(): void
    {
        $this->gatewayMock = $this->getStorageGateway();
        $this->pathGeneratorMock = $this->createMock(PathGenerator::class);
        $this->ioServiceMock = $this->createMock(IOServiceInterface::class);
    }

    /**
     * @return \eZ\Publish\Core\FieldType\BinaryBase\BinaryBaseStorage|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPartlyMockedStorage(): BinaryBaseStorage
    {
        return $this->getMockBuilder(BinaryBaseStorage::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    $this->gatewayMock,
                    $this->ioServiceMock,
                    $this->pathGeneratorMock,
                    $this->createMock(MimeTypeDetector::class),
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
        $storage = $this->getPartlyMockedStorage();

        $this->assertTrue($storage->hasFieldData());
    }

    /**
     * @dataProvider providerOfFieldData
     */
    public function testStoreFieldData(VersionInfo $versionInfo, Field $field): void
    {
        $storage = $this->getPartlyMockedStorage();

        $binaryFileCreateStruct = new BinaryFileCreateStruct([
            'id' => 'qwerty12345',
            'size' => '372949',
            'mimeType' => 'image/jpeg',
        ]);

        $this->ioServiceMock
            ->expects($this->once())
            ->method('newBinaryCreateStructFromLocalFile')
            ->will($this->returnValue($binaryFileCreateStruct));

        $this->pathGeneratorMock
            ->expects($this->once())
            ->method('getStoragePathForField')
            ->with($field, $versionInfo)
            ->willReturn('image/qwerty12345.jpg');

        $this->ioServiceMock
            ->expects($this->once())
            ->method('createBinaryFile')
            ->with($binaryFileCreateStruct)
            ->willReturn(new BinaryFile());

        $this->ioServiceMock
            ->expects($this->any())
            ->method('loadBinaryFile')
            ->with($field->value->externalData['id'])
            ->willReturn(new BinaryFile());

        $storage->storeFieldData($versionInfo, $field, $this->getContext());

        $this->doesNotPerformAssertions();
    }

    /**
     * @dataProvider providerOfFieldData
     */
    public function testCopyLegacyField(VersionInfo $versionInfo, Field $originalField): void
    {
        $storage = $this->getPartlyMockedStorage();

        $field = clone $originalField;
        $field->id = 124;
        $field->value = new FieldValue([
            'externalData' => [
                'fileName' => '1.jpg',
                'downloadCount' => 0,
                'mimeType' => null,
                'uri' => null,
            ],
        ]);

        $storage->copyLegacyField($versionInfo, $field, $originalField, $this->getContext());

        $this->doesNotPerformAssertions();
    }

    public function providerOfFieldData(): array
    {
        $field = new Field();
        $field->id = 123;
        $field->fieldDefinitionId = 231;
        $field->type = 'ezbinaryfile';
        $field->versionNo = 1;
        $field->value = new FieldValue([
            'externalData' => [
                'id' => 'image/809c753a26e11f363cd8c14d824d162a.jpg',
                'path' => '/tmp/phpR4tNSI',
                'inputUri' => '/tmp/phpR4tNSI',
                'fileName' => '1.jpg',
                'fileSize' => '372949',
                'mimeType' => 'image/jpeg',
                'uri' => '/admin/content/download/75/320?version=1',
                'downloadCount' => 0,
            ],
        ]);

        $versionInfo = new VersionInfo([
            'contentInfo' => new ContentInfo([
                'id' => 1,
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
