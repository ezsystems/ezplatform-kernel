<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\Imagine\VariationPurger;

use eZ\Publish\Core\IO\IOConfigProvider;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

/**
 * Iterator for entries in legacy's ezimagefile table.
 *
 * The returned items are id of Image BinaryFile (ez-mountains/mount-aconcagua/605-1-eng-GB/Mount-Aconcagua.jpg).
 */
class LegacyStorageImageFileList implements ImageFileList
{
    /**
     * Last fetched item.
     *
     * @var mixed
     */
    private $item;

    /**
     * Iteration cursor on $statement.
     *
     * @var int
     */
    private $cursor;

    /**
     * Used to get ezimagefile rows.
     *
     * @var \eZ\Bundle\EzPublishCoreBundle\Imagine\VariationPurger\ImageFileRowReader
     */
    private $rowReader;

    /** @var \eZ\Publish\Core\IO\IOConfigProvider */
    private $ioConfigResolver;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        ImageFileRowReader $rowReader,
        IOConfigProvider $ioConfigResolver,
        ConfigResolverInterface $configResolver
    ) {
        $this->ioConfigResolver = $ioConfigResolver;
        $this->rowReader = $rowReader;
        $this->configResolver = $configResolver;
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->item;
    }

    public function next(): void
    {
        $this->fetchRow();
    }

    public function key(): int
    {
        return $this->cursor;
    }

    public function valid(): bool
    {
        return $this->cursor < $this->count();
    }

    public function rewind(): void
    {
        $this->cursor = -1;
        $this->rowReader->init();
        $this->fetchRow();
    }

    public function count(): int
    {
        return $this->rowReader->getCount();
    }

    /**
     * Fetches the next item from the resultset, moves the cursor forward, and removes the prefix from the image id.
     */
    private function fetchRow(): void
    {
        // Folder, relative to the root, where files are stored. Example: var/ezdemo_site/storage
        $storageDir = $this->ioConfigResolver->getLegacyUrlPrefix();
        $prefix = $storageDir . '/' . $this->configResolver->getParameter('image.published_images_dir');
        ++$this->cursor;
        $imageId = $this->rowReader->getRow();

        if (strpos((string)$imageId, $prefix) === 0) {
            $imageId = ltrim(substr($imageId, strlen($prefix)), '/');
        }

        $this->item = $imageId;
    }
}
