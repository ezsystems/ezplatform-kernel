<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\ApiLoader;

use Ibexa\Bundle\Core\ApiLoader\Exception\InvalidStorageEngine;
use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;

/**
 * The storage engine factory.
 */
class StorageEngineFactory
{
    /** @var \Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider */
    private $repositoryConfigurationProvider;

    /**
     * Hash of registered storage engines.
     * Key is the storage engine identifier, value persistence handler itself.
     *
     * @var \Ibexa\Contracts\Core\Persistence\Handler[]
     */
    protected $storageEngines = [];

    public function __construct(RepositoryConfigurationProvider $repositoryConfigurationProvider)
    {
        $this->repositoryConfigurationProvider = $repositoryConfigurationProvider;
    }

    /**
     * Registers $persistenceHandler as a valid storage engine, with identifier $storageEngineIdentifier.
     *
     * Note: It is strongly recommenced to register a lazy persistent handler.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Handler $persistenceHandler
     * @param string $storageEngineIdentifier
     */
    public function registerStorageEngine(PersistenceHandler $persistenceHandler, $storageEngineIdentifier)
    {
        $this->storageEngines[$storageEngineIdentifier] = $persistenceHandler;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Handler[]
     */
    public function getStorageEngines()
    {
        return $this->storageEngines;
    }

    /**
     * Builds storage engine identified by $storageEngineIdentifier (the "alias" attribute in the service tag).
     *
     * @throws \Ibexa\Bundle\Core\ApiLoader\Exception\InvalidStorageEngine
     *
     * @return \Ibexa\Contracts\Core\Persistence\Handler
     */
    public function buildStorageEngine()
    {
        $repositoryConfig = $this->repositoryConfigurationProvider->getRepositoryConfig();

        if (
            !(
                isset($repositoryConfig['storage']['engine'])
                && isset($this->storageEngines[$repositoryConfig['storage']['engine']])
            )
        ) {
            throw new InvalidStorageEngine(
                "Invalid storage engine '{$repositoryConfig['storage']['engine']}'. " .
                'Could not find any service tagged with ezpublish.storageEngine ' .
                "with alias {$repositoryConfig['storage']['engine']}."
            );
        }

        return $this->storageEngines[$repositoryConfig['storage']['engine']];
    }
}

class_alias(StorageEngineFactory::class, 'eZ\Bundle\EzPublishCoreBundle\ApiLoader\StorageEngineFactory');
