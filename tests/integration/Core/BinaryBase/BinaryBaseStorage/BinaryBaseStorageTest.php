<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\BinaryBase\BinaryBaseStorage;

use Ibexa\Contracts\Core\FieldType\BinaryBase\PathGenerator;
use Ibexa\Contracts\Core\IO\MimeTypeDetector;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\FieldType\BinaryBase\BinaryBaseStorage;
use Ibexa\Core\FieldType\BinaryBase\BinaryBaseStorage\Gateway;
use Ibexa\Core\FieldType\BinaryFile\BinaryFileStorage\Gateway\DoctrineStorage;
use Ibexa\Core\IO\IOServiceInterface;
use Ibexa\Core\IO\Values\BinaryFile;
use Ibexa\Core\IO\Values\BinaryFileCreateStruct;
use Ibexa\Tests\Integration\Core\BaseCoreFieldTypeIntegrationTest;

class BinaryBaseStorageTest extends BaseCoreFieldTypeIntegrationTest
{
    /** @var \Ibexa\Core\FieldType\BinaryBase\BinaryBaseStorage\Gateway|\PHPUnit\Framework\MockObject\MockObject */
    protected $gateway;

    /** @var \Ibexa\Contracts\Core\FieldType\BinaryBase\PathGenerator|\PHPUnit\Framework\MockObject\MockObject */
    protected $pathGeneratorMock;

    /** @var \Ibexa\Core\IO\IOServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $ioServiceMock;

    /** @var \Ibexa\Core\FieldType\BinaryBase\BinaryBaseStorage|\PHPUnit\Framework\MockObject\MockObject */
    protected $storage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = $this->getStorageGateway();
        $this->pathGeneratorMock = $this->createMock(PathGenerator::class);
        $this->ioServiceMock = $this->createMock(IOServiceInterface::class);
        $this->storage = $this->getMockBuilder(BinaryBaseStorage::class)
            ->onlyMethods([])
            ->setConstructorArgs(
                [
                    $this->gateway,
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

class_alias(BinaryBaseStorageTest::class, 'eZ\Publish\Core\FieldType\Tests\Integration\BinaryBase\BinaryBaseStorage\BinaryBaseStorageTest');
