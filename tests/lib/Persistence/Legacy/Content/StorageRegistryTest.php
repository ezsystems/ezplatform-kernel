<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content;

use Ibexa\Contracts\Core\FieldType\FieldStorage;
use Ibexa\Core\FieldType\NullStorage;
use Ibexa\Core\Persistence\Legacy\Content\StorageRegistry;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\StorageRegistry
 */
class StorageRegistryTest extends TestCase
{
    private const TYPE_NAME = 'some-type';

    public function testRegister(): void
    {
        $storage = $this->getStorageMock();
        $registry = new StorageRegistry([self::TYPE_NAME => $storage]);

        $this->assertSame($storage, $registry->getStorage(self::TYPE_NAME));
    }

    public function testGetStorage()
    {
        $storage = $this->getStorageMock();
        $registry = new StorageRegistry([self::TYPE_NAME => $storage]);

        $res = $registry->getStorage(self::TYPE_NAME);

        $this->assertSame(
            $storage,
            $res
        );
    }

    public function testGetNotFound()
    {
        $registry = new StorageRegistry([]);
        self::assertInstanceOf(
            NullStorage::class,
            $registry->getStorage('not-found')
        );
    }

    /**
     * Returns a mock for Storage.
     *
     * @return \Ibexa\Contracts\Core\FieldType\FieldStorage
     */
    protected function getStorageMock()
    {
        return $this->createMock(FieldStorage::class);
    }
}

class_alias(StorageRegistryTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\StorageRegistryTest');
