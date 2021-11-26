<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\IO\IOBinarydataHandler;

use Ibexa\Bundle\IO\ApiLoader\HandlerRegistry;
use Ibexa\Contracts\Core\IO\BinaryFileCreateStruct;
use Ibexa\Core\IO\IOBinarydataHandler;
use Ibexa\Core\MVC\ConfigResolverInterface;

/**
 * @internal
 */
final class SiteAccessDependentBinaryDataHandler implements IOBinaryDataHandler
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

    private function getHandler(): IOBinarydataHandler
    {
        return $this->dataHandlerRegistry->getConfiguredHandler(
            $this->configResolver->getParameter('io.binarydata_handler')
        );
    }

    public function create(BinaryFileCreateStruct $binaryFileCreateStruct)
    {
        return $this->getHandler()->create($binaryFileCreateStruct);
    }

    public function delete($spiBinaryFileId)
    {
        return $this->getHandler()->delete($spiBinaryFileId);
    }

    public function getContents($spiBinaryFileId)
    {
        return $this->getHandler()->getContents($spiBinaryFileId);
    }

    public function getResource($spiBinaryFileId)
    {
        return $this->getHandler()->getResource($spiBinaryFileId);
    }

    public function getUri($spiBinaryFileId)
    {
        return $this->getHandler()->getUri($spiBinaryFileId);
    }

    public function getIdFromUri($binaryFileUri)
    {
        return $this->getHandler()->getIdFromUri($binaryFileUri);
    }

    public function deleteDirectory($spiPath)
    {
        return $this->getHandler()->deleteDirectory($spiPath);
    }
}

class_alias(SiteAccessDependentBinaryDataHandler::class, 'eZ\Publish\Core\IO\IOBinarydataHandler\SiteAccessDependentBinaryDataHandler');
