<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\IO\Migration;

use Ibexa\Bundle\IO\ApiLoader\HandlerRegistry;
use Psr\Log\LoggerInterface;

/**
 * The migration handler sets up from/to IO data handlers, and provides logging, for file migrators and listers.
 */
abstract class MigrationHandler implements MigrationHandlerInterface
{
    /** @var \Ibexa\Bundle\IO\ApiLoader\HandlerRegistry */
    private $metadataHandlerRegistry;

    /** @var \Ibexa\Bundle\IO\ApiLoader\HandlerRegistry */
    private $binarydataHandlerRegistry;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \Ibexa\Core\IO\IOMetadataHandler */
    protected $fromMetadataHandler;

    /** @var \Ibexa\Core\IO\IOBinarydataHandler */
    protected $fromBinarydataHandler;

    /** @var \Ibexa\Core\IO\IOMetadataHandler */
    protected $toMetadataHandler;

    /** @var \Ibexa\Core\IO\IOBinarydataHandler */
    protected $toBinarydataHandler;

    public function __construct(
        HandlerRegistry $metadataHandlerRegistry,
        HandlerRegistry $binarydataHandlerRegistry,
        LoggerInterface $logger = null
    ) {
        $this->metadataHandlerRegistry = $metadataHandlerRegistry;
        $this->binarydataHandlerRegistry = $binarydataHandlerRegistry;
        $this->logger = $logger;
    }

    public function setIODataHandlersByIdentifiers(
        $fromMetadataHandlerIdentifier,
        $fromBinarydataHandlerIdentifier,
        $toMetadataHandlerIdentifier,
        $toBinarydataHandlerIdentifier
    ) {
        $this->fromMetadataHandler = $this->metadataHandlerRegistry->getConfiguredHandler($fromMetadataHandlerIdentifier);
        $this->fromBinarydataHandler = $this->binarydataHandlerRegistry->getConfiguredHandler($fromBinarydataHandlerIdentifier);
        $this->toMetadataHandler = $this->metadataHandlerRegistry->getConfiguredHandler($toMetadataHandlerIdentifier);
        $this->toBinarydataHandler = $this->binarydataHandlerRegistry->getConfiguredHandler($toBinarydataHandlerIdentifier);

        return $this;
    }

    final protected function logError($message)
    {
        if (isset($this->logger)) {
            $this->logger->error($message);
        }
    }

    final protected function logInfo($message)
    {
        if (isset($this->logger)) {
            $this->logger->info($message);
        }
    }

    final protected function logMissingFile($id)
    {
        $this->logInfo("File with id $id not found");
    }
}

class_alias(MigrationHandler::class, 'eZ\Bundle\EzPublishIOBundle\Migration\MigrationHandler');
