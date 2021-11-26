<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Url;

use Ibexa\Contracts\Core\FieldType\GatewayBasedStorage;
use Ibexa\Contracts\Core\FieldType\StorageGateway;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Psr\Log\LoggerInterface;

/**
 * Converter for Url field type external storage.
 */
class UrlStorage extends GatewayBasedStorage
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Ibexa\Core\FieldType\Url\UrlStorage\Gateway */
    protected $gateway;

    /**
     * Construct from gateways.
     *
     * @param \Ibexa\Contracts\Core\FieldType\StorageGateway $gateway
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(StorageGateway $gateway, LoggerInterface $logger = null)
    {
        parent::__construct($gateway);
        $this->logger = $logger;
    }

    /**
     * @see \Ibexa\Contracts\Core\FieldType\FieldStorage
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     * @param array $context
     *
     * @return bool|mixed
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        $url = $field->value->externalData;

        if (empty($url)) {
            return false;
        }

        $map = $this->gateway->getUrlIdMap([$url]);

        if (isset($map[$url])) {
            $urlId = $map[$url];
        } else {
            $urlId = $this->gateway->insertUrl($url);
        }

        $this->gateway->linkUrl($urlId, $field->id, $versionInfo->versionNo);

        $this->gateway->unlinkUrl(
            $field->id,
            $versionInfo->versionNo,
            [$urlId]
        );

        $field->value->data['urlId'] = $urlId;

        // Signals that the Value has been modified and that an update is to be performed
        return true;
    }

    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        $id = $field->value->data['urlId'];
        if (empty($id)) {
            $field->value->externalData = null;

            return;
        }

        $map = $this->gateway->getIdUrlMap([$id]);

        // URL id is not in the DB
        if (!isset($map[$id]) && isset($this->logger)) {
            $this->logger->error("URL with ID '{$id}' not found");
        }

        $field->value->externalData = isset($map[$id]) ? $map[$id] : '';
    }

    /**
     * Deletes field data for all $fieldIds in the version identified by
     * $versionInfo.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param array $fieldIds
     * @param array $context
     *
     * @return bool
     */
    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context)
    {
        foreach ($fieldIds as $fieldId) {
            $this->gateway->unlinkUrl($fieldId, $versionInfo->versionNo);
        }
    }

    /**
     * Checks if field type has external data to deal with.
     *
     * @return bool
     */
    public function hasFieldData()
    {
        return true;
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     * @param array $context
     *
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
    }
}

class_alias(UrlStorage::class, 'eZ\Publish\Core\FieldType\Url\UrlStorage');
