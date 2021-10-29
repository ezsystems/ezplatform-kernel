<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\IO\Migration\FileLister;

use Ibexa\Bundle\IO\ApiLoader\HandlerRegistry;
use Ibexa\Bundle\IO\Migration\FileListerInterface;
use Ibexa\Bundle\IO\Migration\MigrationHandler;
use Ibexa\Core\IO\Exception\BinaryFileNotFoundException;
use Iterator;
use LimitIterator;
use Psr\Log\LoggerInterface;

class BinaryFileLister extends MigrationHandler implements FileListerInterface
{
    /** @var \Ibexa\Bundle\IO\Migration\FileLister\FileIteratorInterface */
    private $fileList;

    /** @var string Directory where files are stored, within the storage dir. Example: 'original' */
    private $filesDir;

    /**
     * @param \Ibexa\Bundle\IO\ApiLoader\HandlerRegistry $metadataHandlerRegistry
     * @param \Ibexa\Bundle\IO\ApiLoader\HandlerRegistry $binarydataHandlerRegistry
     * @param \Psr\Log\LoggerInterface|null $logger
     * @param \Iterator $fileList
     * @param string $filesDir Directory where files are stored, within the storage dir. Example: 'original'
     */
    public function __construct(
        HandlerRegistry $metadataHandlerRegistry,
        HandlerRegistry $binarydataHandlerRegistry,
        LoggerInterface $logger = null,
        Iterator $fileList,
        $filesDir
    ) {
        $this->fileList = $fileList;
        $this->filesDir = $filesDir;

        $this->fileList->rewind();

        parent::__construct($metadataHandlerRegistry, $binarydataHandlerRegistry, $logger);
    }

    public function countFiles()
    {
        return count($this->fileList);
    }

    public function loadMetadataList($limit = null, $offset = null)
    {
        $metadataList = [];
        $fileLimitList = new LimitIterator($this->fileList, $offset, $limit);

        foreach ($fileLimitList as $fileId) {
            try {
                $metadataList[] = $this->fromMetadataHandler->load($this->filesDir . '/' . $fileId);
            } catch (BinaryFileNotFoundException $e) {
                $this->logMissingFile($fileId);

                continue;
            }
        }

        return $metadataList;
    }
}

class_alias(BinaryFileLister::class, 'eZ\Bundle\EzPublishIOBundle\Migration\FileLister\BinaryFileLister');
