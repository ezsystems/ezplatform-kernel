<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\FieldType\Image\ImageStorage;

use eZ\Publish\SPI\FieldType\StorageGateway;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

/**
 * Image Field Type external storage gateway.
 */
abstract class Gateway extends StorageGateway
{
    /**
     * Returns the node path string of $versionInfo.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     *
     * @return string
     */
    abstract public function getNodePathString(VersionInfo $versionInfo);

    /**
     * return true if image reference already exists (avoid image duplication for the same contentobject_attribute_id) false otherwise
     *
     * @param string $uri File IO uri
     * @param mixed $fieldId
     * @return bool
     */
    abstract public function hasImageReference($uri, $fieldId): bool;

    /**
     * Stores a reference to the image in $path for $fieldId.
     *
     * @param string $uri File IO uri
     * @param mixed $fieldId
     */
    abstract public function storeImageReference($uri, $fieldId);

    /**
     * Returns a the XML content stored for the given $fieldIds.
     *
     * @param int $versionNo
     * @param array $fieldIds
     *
     * @return array
     */
    abstract public function getXmlForImages($versionNo, array $fieldIds);

    /**
     * Removes all references from $fieldId to a path that starts with $path.
     *
     * @param string $uri File IO uri (not legacy uri)
     * @param int $versionNo
     * @param mixed $fieldId
     */
    abstract public function removeImageReferences($uri, $versionNo, $fieldId);

    /**
     * Returns the number of recorded references to the given $path.
     *
     * @param string $uri File IO uri (not legacy uri)
     *
     * @return int
     */
    abstract public function countImageReferences($uri);

    /**
     * Returns true if there is reference to the given $uri.
     */
    abstract public function isImageReferenced(string $uri): bool;

    /**
     * Returns the public uris for the images stored in $xml.
     */
    abstract public function extractFilesFromXml($xml);

    abstract public function getAllVersionsImageXmlForFieldId(int $fieldId): array;

    abstract public function updateImageData(int $fieldId, int $versionNo, string $xml): void;

    abstract public function getImagesData(int $offset, int $limit): array;

    abstract public function updateImagePath(int $fieldId, string $oldPath, string $newPath): void;

    abstract public function countDistinctImagesData(): int;
}
