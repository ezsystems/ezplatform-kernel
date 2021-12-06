<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\IO\IOBinarydataHandler;

use Ibexa\Bundle\IO\ApiLoader\HandlerRegistry;
use Ibexa\Contracts\Core\IO\BinaryFileCreateStruct;
use Ibexa\Core\IO\IOMetadataHandler;
use Ibexa\Core\MVC\ConfigResolverInterface;

/**
 * @internal
 */
final class SiteAccessDependentMetadataHandler implements IOMetadataHandler
{
    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Bundle\IO\ApiLoader\HandlerRegistry */
    private $dataHandlerRegistry;

    public function __construct(
        ConfigResolverInterface $configResolver,
        HandlerRegistry $dataHandlerRegistry
    ) {
        $this->configResolver = $configResolver;
        $this->dataHandlerRegistry = $dataHandlerRegistry;
    }

    private function getHandler(): IOMetadataHandler
    {
        return $this->dataHandlerRegistry->getConfiguredHandler(
            $this->configResolver->getParameter('io.metadata_handler')
        );
    }

    public function create(BinaryFileCreateStruct $spiBinaryFileCreateStruct)
    {
        return $this->getHandler()->create($spiBinaryFileCreateStruct);
    }

    public function delete($spiBinaryFileId)
    {
        return $this->getHandler()->delete($spiBinaryFileId);
    }

    public function load($spiBinaryFileId)
    {
        return $this->getHandler()->load($spiBinaryFileId);
    }

    public function exists($spiBinaryFileId)
    {
        return $this->getHandler()->exists($spiBinaryFileId);
    }

    public function getMimeType($spiBinaryFileId)
    {
        return $this->getHandler()->getMimeType($spiBinaryFileId);
    }

    public function deleteDirectory($spiPath)
    {
        return $this->getHandler()->deleteDirectory($spiPath);
    }
}

class_alias(SiteAccessDependentMetadataHandler::class, 'eZ\Publish\Core\IO\IOBinarydataHandler\SiteAccessDependentMetadataHandler');
