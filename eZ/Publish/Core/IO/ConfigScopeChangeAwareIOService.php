<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\IO;

use eZ\Publish\Core\IO\Values\BinaryFile;
use eZ\Publish\Core\IO\Values\BinaryFileCreateStruct;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\SPI\MVC\EventSubscriber\ConfigScopeChangeSubscriber;

class ConfigScopeChangeAwareIOService implements IOServiceInterface, ConfigScopeChangeSubscriber
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    protected $configResolver;

    /** @var \eZ\Publish\Core\IO\IOServiceInterface */
    protected $innerIOService;

    /** @var string */
    protected $prefixParameterName;

    public function __construct(
        ConfigResolverInterface $configResolver,
        IOServiceInterface $innerIOService,
        string $prefixParameterName
    ) {
        $this->configResolver = $configResolver;
        $this->innerIOService = $innerIOService;
        $this->prefixParameterName = $prefixParameterName;
    }

    public function setPrefix($prefix)
    {
        $this->innerIOService->setPrefix($prefix);
    }

    public function getExternalPath($internalId)
    {
        return $this->innerIOService->getExternalPath($internalId);
    }

    public function newBinaryCreateStructFromLocalFile($localFile)
    {
        return $this->innerIOService->newBinaryCreateStructFromLocalFile($localFile);
    }

    public function exists($binaryFileId)
    {
        return $this->innerIOService->exists($binaryFileId);
    }

    public function getInternalPath($externalId)
    {
        return $this->innerIOService->getInternalPath($externalId);
    }

    public function loadBinaryFile($binaryFileId)
    {
        return $this->innerIOService->loadBinaryFile($binaryFileId);
    }

    public function loadBinaryFileByUri($binaryFileUri)
    {
        return $this->innerIOService->loadBinaryFileByUri($binaryFileUri);
    }

    public function getFileContents(BinaryFile $binaryFile)
    {
        return $this->innerIOService->getFileContents($binaryFile);
    }

    public function createBinaryFile(BinaryFileCreateStruct $binaryFileCreateStruct)
    {
        return $this->innerIOService->createBinaryFile($binaryFileCreateStruct);
    }

    public function getUri($binaryFileId)
    {
        return $this->innerIOService->getUri($binaryFileId);
    }

    public function getMimeType($binaryFileId)
    {
        return $this->innerIOService->getMimeType($binaryFileId);
    }

    public function getFileInputStream(BinaryFile $binaryFile)
    {
        return $this->innerIOService->getFileInputStream($binaryFile);
    }

    public function deleteBinaryFile(BinaryFile $binaryFile)
    {
        return $this->innerIOService->deleteBinaryFile($binaryFile);
    }

    public function newBinaryCreateStructFromUploadedFile(array $uploadedFile)
    {
        return $this->innerIOService->newBinaryCreateStructFromUploadedFile($uploadedFile);
    }

    public function deleteDirectory($path)
    {
        return $this->innerIOService->deleteDirectory($path);
    }

    public function onConfigScopeChange(SiteAccess $siteAccess): void
    {
        $this->setPrefix($this->configResolver->getParameter($this->prefixParameterName));
    }
}
