<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\FieldType;

use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;

/**
 * Field Type External Storage gateway base class.
 *
 * @template T of \Ibexa\Contracts\Core\FieldType\StorageGateway
 */
abstract class GatewayBasedStorage implements FieldStorage
{
    /**
     * Field Type External Storage Gateway.
     *
     * @var \Ibexa\Contracts\Core\FieldType\StorageGateway
     * @phpstan-var T
     */
    protected $gateway;

    /**
     * @param \Ibexa\Contracts\Core\FieldType\StorageGateway $gateway
     * @phpstan-param T $gateway
     */
    public function __construct(StorageGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * This method is used exclusively by Legacy Storage to copy external data of existing field in main language to
     * the untranslatable field not passed in create or update struct, but created implicitly in storage layer.
     *
     * By default the method falls back to the {@link \Ibexa\Contracts\Core\FieldType\FieldStorage::storeFieldData()}.
     * External storages implement this method as needed.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $originalField
     * @param array $context
     *
     * @return bool|null Same as {@link \Ibexa\Contracts\Core\FieldType\FieldStorage::storeFieldData()}.
     */
    public function copyLegacyField(VersionInfo $versionInfo, Field $field, Field $originalField, array $context)
    {
        return $this->storeFieldData($versionInfo, $field, $context);
    }
}

class_alias(GatewayBasedStorage::class, 'eZ\Publish\SPI\FieldType\GatewayBasedStorage');
