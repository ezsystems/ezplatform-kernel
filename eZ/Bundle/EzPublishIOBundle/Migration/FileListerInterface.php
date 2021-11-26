<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishIOBundle\Migration;

interface FileListerInterface extends MigrationHandlerInterface
{
    /**
     * Count the number of existing files.
     *
     * @return int|null Number of files, or null if they cannot be counted
     */
    public function countFiles();

    /**
     * Loads and returns metadata for files, optionally limited by $limit and $offset.
     *
     * @param int|null $limit The number of files to load data for, or null
     * @param int|null $offset The offset used when loading in batches, or null
     *
     * @return \eZ\Publish\SPI\IO\BinaryFile[]
     */
    public function loadMetadataList($limit = null, $offset = null);
}
