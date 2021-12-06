<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content;

use Ibexa\Contracts\Core\FieldType\FieldStorage;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Core\Persistence\Legacy\Content\StorageHandler;
use Ibexa\Core\Persistence\Legacy\Content\StorageRegistry;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\StorageHandler
 */
class StorageHandlerTest extends TestCase
{
    /**
     * StorageRegistry mock.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\StorageRegistry
     */
    protected $storageRegistryMock;

    /**
     * StorageHandler to test.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\StorageHandler
     */
    protected $storageHandler;

    /**
     * Mock for external storage.
     *
     * @var \Ibexa\Contracts\Core\FieldType\FieldStorage
     */
    protected $storageMock;

    /**
     * Mock for versionInfo.
     *
     * @var \Ibexa\Core\Repository\Values\Content\VersionInfo
     */
    protected $versionInfoMock;

    public function testStoreFieldData()
    {
        $storageMock = $this->getStorageMock();
        $storageRegistryMock = $this->getStorageRegistryMock();

        $storageMock->expects($this->once())
            ->method('storeFieldData')
            ->with(
                $this->isInstanceOf(VersionInfo::class),
                $this->isInstanceOf(Field::class),
                $this->equalTo($this->getContextMock())
            );

        $storageRegistryMock->expects($this->once())
            ->method('getStorage')
            ->with($this->equalTo('foobar'))
            ->will($this->returnValue($storageMock));

        $field = new Field();
        $field->type = 'foobar';
        $field->value = new FieldValue();

        $handler = $this->getStorageHandler();
        $handler->storeFieldData($this->getVersionInfoMock(), $field);
    }

    public function testGetFieldDataAvailable()
    {
        $storageMock = $this->getStorageMock();
        $storageRegistryMock = $this->getStorageRegistryMock();

        $storageMock->expects($this->once())
            ->method('hasFieldData')
            ->will($this->returnValue(true));
        $storageMock->expects($this->once())
            ->method('getFieldData')
            ->with(
                $this->isInstanceOf(VersionInfo::class),
                $this->isInstanceOf(Field::class),
                $this->equalTo($this->getContextMock())
            );

        $storageRegistryMock->expects($this->once())
            ->method('getStorage')
            ->with($this->equalTo('foobar'))
            ->will($this->returnValue($storageMock));

        $field = new Field();
        $field->type = 'foobar';
        $field->value = new FieldValue();

        $handler = $this->getStorageHandler();
        $handler->getFieldData($this->getVersionInfoMock(), $field);
    }

    public function testGetFieldDataNotAvailable()
    {
        $storageMock = $this->getStorageMock();
        $storageRegistryMock = $this->getStorageRegistryMock();

        $storageMock->expects($this->once())
            ->method('hasFieldData')
            ->will($this->returnValue(false));
        $storageMock->expects($this->never())
            ->method('getFieldData');

        $storageRegistryMock->expects($this->once())
            ->method('getStorage')
            ->with($this->equalTo('foobar'))
            ->will($this->returnValue($storageMock));

        $field = new Field();
        $field->type = 'foobar';
        $field->value = new FieldValue();

        $handler = $this->getStorageHandler();
        $handler->getFieldData($this->getVersionInfoMock(), $field);
    }

    public function testDeleteFieldData()
    {
        $storageMock = $this->getStorageMock();
        $storageRegistryMock = $this->getStorageRegistryMock();

        $storageMock->expects($this->once())
            ->method('deleteFieldData')
            ->with(
                $this->isInstanceOf(VersionInfo::class),
                $this->equalTo([1, 2, 3]),
                $this->equalTo($this->getContextMock())
            );

        $storageRegistryMock->expects($this->once())
            ->method('getStorage')
            ->with($this->equalTo('foobar'))
            ->will($this->returnValue($storageMock));

        $handler = $this->getStorageHandler();
        $handler->deleteFieldData('foobar', new VersionInfo(), [1, 2, 3]);
    }

    /**
     * Returns the StorageHandler to test.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\StorageHandler
     */
    protected function getStorageHandler()
    {
        if (!isset($this->storageHandler)) {
            $this->storageHandler = new StorageHandler(
                $this->getStorageRegistryMock(),
                $this->getContextMock()
            );
        }

        return $this->storageHandler;
    }

    /**
     * Returns a context mock.
     *
     * @return array
     */
    protected function getContextMock()
    {
        return [23, 42];
    }

    /**
     * Returns a StorageRegistry mock.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\StorageRegistry
     */
    protected function getStorageRegistryMock()
    {
        if (!isset($this->storageRegistryMock)) {
            $this->storageRegistryMock = $this->getMockBuilder(StorageRegistry::class)
                ->setConstructorArgs([[]])
                ->setMethods([])
                ->getMock();
        }

        return $this->storageRegistryMock;
    }

    /**
     * Returns a Storage mock.
     *
     * @return \Ibexa\Contracts\Core\FieldType\FieldStorage
     */
    protected function getStorageMock()
    {
        if (!isset($this->storageMock)) {
            $this->storageMock = $this->createMock(FieldStorage::class);
        }

        return $this->storageMock;
    }

    protected function getVersionInfoMock()
    {
        if (!isset($this->versionInfoMock)) {
            $this->versionInfoMock = $this->createMock(VersionInfo::class);
        }

        return $this->versionInfoMock;
    }
}

class_alias(StorageHandlerTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\StorageHandlerTest');
