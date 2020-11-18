<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\FieldType\Image;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Utils\DeprecationWarnerInterface as DeprecationWarner;
use eZ\Publish\SPI\FieldType\GatewayBasedStorage;
use eZ\Publish\Core\IO\IOServiceInterface;
use eZ\Publish\Core\IO\MetadataHandler;
use eZ\Publish\SPI\FieldType\StorageGateway;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

/**
 * Converter for Image field type external storage.
 */
class ImageStorage extends GatewayBasedStorage
{
    /** @var \eZ\Publish\Core\IO\IOServiceInterface */
    protected $ioService;

    /** @var \eZ\Publish\Core\FieldType\Image\PathGenerator */
    protected $pathGenerator;

    /** @var \eZ\Publish\Core\IO\MetadataHandler */
    protected $imageSizeMetadataHandler;

    /** @var \eZ\Publish\Core\Base\Utils\DeprecationWarnerInterface */
    private $deprecationWarner;

    /** @var \eZ\Publish\Core\FieldType\Image\AliasCleanerInterface */
    protected $aliasCleaner;

    /** @var \eZ\Publish\Core\FieldType\Image\ImageStorage\Gateway */
    protected $gateway;

    public function __construct(
        StorageGateway $gateway,
        IOServiceInterface $ioService,
        PathGenerator $pathGenerator,
        MetadataHandler $imageSizeMetadataHandler,
        DeprecationWarner $deprecationWarner,
        AliasCleanerInterface $aliasCleaner
    ) {
        parent::__construct($gateway);
        $this->ioService = $ioService;
        $this->pathGenerator = $pathGenerator;
        $this->imageSizeMetadataHandler = $imageSizeMetadataHandler;
        $this->deprecationWarner = $deprecationWarner;
        $this->aliasCleaner = $aliasCleaner;
    }

    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        $contentMetaData = [
            'fieldId' => $field->id,
            'versionNo' => $versionInfo->versionNo,
            'languageCode' => $field->languageCode,
            'additionalData' => $field->value->externalData['additionalData'] ?? [],
        ];

        // new image
        if (isset($field->value->externalData)) {
            $targetPath = sprintf(
                '%s/%s',
                $this->pathGenerator->getStoragePathForField(
                    $field->id,
                    $versionInfo->versionNo,
                    $field->languageCode
                ),
                $field->value->externalData['fileName']
            );

            if (isset($field->value->externalData['inputUri'])) {
                $localFilePath = $field->value->externalData['inputUri'];
                unset($field->value->externalData['inputUri']);

                $binaryFileCreateStruct = $this->ioService->newBinaryCreateStructFromLocalFile($localFilePath);
                $binaryFileCreateStruct->id = $targetPath;
                $binaryFile = $this->ioService->createBinaryFile($binaryFileCreateStruct);

                $imageSize = getimagesize($localFilePath);
                $field->value->externalData['width'] = $imageSize[0];
                $field->value->externalData['height'] = $imageSize[1];
            } elseif (isset($field->value->externalData['id'])) {
                $binaryFile = $this->ioService->loadBinaryFile($field->value->externalData['id']);
            } elseif ($this->ioService->exists($targetPath)) {
                $binaryFile = $this->ioService->loadBinaryFile($targetPath);
            } else {
                throw new InvalidArgumentException(
                    'inputUri',
                    'No source image could be obtained from the given external data'
                );
            }

            $field->value->externalData['imageId'] = $this->buildImageId($versionInfo, $field);
            $field->value->externalData['uri'] = $binaryFile->uri;
            $field->value->externalData['id'] = $binaryFile->id;
            $field->value->externalData['mime'] = $this->ioService->getMimeType($binaryFile->id);

            $field->value->data = array_merge(
                $field->value->externalData,
                $contentMetaData
            );

            $field->value->externalData = null;
        } else { // existing image from another version
            if ($field->value->data === null) {
                // Store empty value only with content meta data
                $field->value->data = $contentMetaData;

                return false;
            }

            $this->ioService->loadBinaryFile($field->value->data['id']);

            $field->value->data = array_merge(
                $field->value->data,
                $contentMetaData
            );
            $field->value->externalData = null;
        }

        $this->gateway->storeImageReference($field->value->data['uri'], $field->id);

        // Data has been updated and needs to be stored!
        return true;
    }

    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        if ($field->value->data !== null) {
            $field->value->data['imageId'] = $this->buildImageId($versionInfo, $field);
            $binaryFile = $this->ioService->loadBinaryFile($field->value->data['id']);
            $field->value->data['id'] = $binaryFile->id;
            $field->value->data['fileSize'] = $binaryFile->size;
            $field->value->data['uri'] = $binaryFile->uri;
        }
    }

    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context)
    {
        $fieldXmls = $this->gateway->getXmlForImages($versionInfo->versionNo, $fieldIds);

        foreach ($fieldXmls as $fieldId => $xml) {
            $storedFiles = $this->gateway->extractFilesFromXml($xml);
            if ($storedFiles === null) {
                continue;
            }

            foreach ($storedFiles as $storedFilePath) {
                $this->gateway->removeImageReferences($storedFilePath, $versionInfo->versionNo, $fieldId);
                if ($this->gateway->countImageReferences($storedFilePath) === 0) {
                    $binaryFile = $this->ioService->loadBinaryFileByUri($storedFilePath);
                    // remove aliases (real path is prepended with alias prefixes)
                    $this->aliasCleaner->removeAliases($binaryFile->id);
                    // delete original file
                    $this->ioService->deleteBinaryFile($binaryFile);
                }
            }
        }
    }

    public function hasFieldData()
    {
        return true;
    }

    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
        // @todo: Correct?
        return null;
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     *
     * @return string
     */
    private function buildImageId(VersionInfo $versionInfo, Field $field)
    {
        return sprintf(
            '%s-%s-%s',
            $versionInfo->contentInfo->id, $field->id, $versionInfo->versionNo
        );
    }
}
