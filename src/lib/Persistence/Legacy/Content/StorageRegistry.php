<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Content;

use Ibexa\Contracts\Core\FieldType\FieldStorage;
use Ibexa\Core\FieldType\NullStorage;

/**
 * Registry for external storages.
 */
class StorageRegistry
{
    /** @var \Ibexa\Contracts\Core\FieldType\FieldStorage[] */
    protected $storageMap;

    /**
     * @param array $storageMap A map where key is field type name, and value is
     *              a callable factory to get FieldStorage OR FieldStorage object
     */
    public function __construct(array $storageMap = [])
    {
        $this->storageMap = $storageMap;
    }

    public function register(string $typeName, FieldStorage $storage): void
    {
        $this->storageMap[$typeName] = $storage;
    }

    public function getStorage(string $typeName): FieldStorage
    {
        if (!isset($this->storageMap[$typeName])) {
            $this->storageMap[$typeName] = new NullStorage();
        }

        return $this->storageMap[$typeName];
    }
}

class_alias(StorageRegistry::class, 'eZ\Publish\Core\Persistence\Legacy\Content\StorageRegistry');
