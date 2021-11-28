<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Base\Container\Compiler\Stubs;

use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Core\FieldType\GatewayBasedStorage;

/**
 * Stub implementation of GatewayBasedStorage.
 */
class GatewayBasedStorageHandler extends GatewayBasedStorage
{
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
    }

    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
    }

    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context)
    {
    }

    public function hasFieldData()
    {
    }

    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
    }
}

class_alias(GatewayBasedStorageHandler::class, 'eZ\Publish\Core\Base\Tests\Container\Compiler\Stubs\GatewayBasedStorageHandler');
