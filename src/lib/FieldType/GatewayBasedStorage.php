<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType;

use Ibexa\Contracts\Core\FieldType\FieldStorage;
use Ibexa\Contracts\Core\FieldType\GatewayBasedStorage as SPIGatewayBasedStorage;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;

/**
 * Storage gateway base class to be used by FieldType storages.
 *
 * @deprecated Since 6.11. Use {@link \Ibexa\Contracts\Core\FieldType\GatewayBasedStorage}
 *
 * This class gives a common basis to realized gateway based storage
 * dispatching. It is intended to deal as a base class for FieldType storages,
 * giving a common infrastructure to handle multiple gateways, based on the
 * context provided by the SPI.
 *
 * The method {@link getGateway()} is used in derived classes to retrieve the
 * correct gateway implementation, based on the context.
 */
abstract class GatewayBasedStorage implements FieldStorage
{
    /**
     * Gateways.
     *
     * @var \Ibexa\Core\FieldType\StorageGateway[]
     */
    protected $gateways;

    /**
     * Construct from gateways.
     *
     * @param \Ibexa\Core\FieldType\StorageGateway[] $gateways
     */
    public function __construct(array $gateways = [])
    {
        @trigger_error(
            sprintf(
                '%s extends deprecated %s. Extend %s instead',
                static::class,
                self::class,
                SPIGatewayBasedStorage::class
            ),
            E_USER_DEPRECATED
        );

        foreach ($gateways as $identifier => $gateway) {
            $this->addGateway($identifier, $gateway);
        }
    }

    /**
     * Adds a storage $gateway assigned to the given $identifier.
     *
     * @param string $identifier
     * @param \Ibexa\Core\FieldType\StorageGateway $gateway
     */
    public function addGateway($identifier, StorageGateway $gateway)
    {
        $this->gateways[$identifier] = $gateway;
    }

    /**
     * Retrieve the fitting gateway, base on the identifier in $context.
     *
     * @deprecated Since 6.11. Retrieving gateway based on $context is deprecated
     *             and will be removed in 7.0. Inject gateway directly into FieldStorage
     *
     * @param array $context
     *
     * @return \Ibexa\Core\FieldType\StorageGateway
     */
    protected function getGateway(array $context)
    {
        @trigger_error(
            sprintf(
                '%s: Retrieving gateway based on $context is deprecated and will be removed in 7.0. Inject gateway directly into FieldStorage',
                static::class
            ),
            E_USER_DEPRECATED
        );

        if (!isset($this->gateways[$context['identifier']])) {
            throw new \OutOfBoundsException("No gateway for ${context['identifier']} available.");
        }

        $gateway = $this->gateways[$context['identifier']];
        $gateway->setConnection($context['connection']);

        return $gateway;
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

class_alias(GatewayBasedStorage::class, 'eZ\Publish\Core\FieldType\GatewayBasedStorage');
