<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\ApiLoader;

use Ibexa\Bundle\Core\ApiLoader\Exception\InvalidSearchEngine;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;

/**
 * The search engine factory.
 */
class SearchEngineFactory
{
    /** @var \Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider */
    private $repositoryConfigurationProvider;

    /**
     * Hash of registered search engines.
     * Key is the search engine identifier, value search handler itself.
     *
     * @var \Ibexa\Contracts\Core\Search\Handler[]
     */
    protected $searchEngines = [];

    public function __construct(RepositoryConfigurationProvider $repositoryConfigurationProvider)
    {
        $this->repositoryConfigurationProvider = $repositoryConfigurationProvider;
    }

    /**
     * Registers $searchHandler as a valid search engine with identifier $searchEngineIdentifier.
     *
     * Note It is strongly recommended to register a lazy persistent handler.
     *
     * @param \Ibexa\Contracts\Core\Search\Handler $searchHandler
     * @param string $searchEngineIdentifier
     */
    public function registerSearchEngine(SearchHandler $searchHandler, $searchEngineIdentifier)
    {
        $this->searchEngines[$searchEngineIdentifier] = $searchHandler;
    }

    /**
     * Returns registered search engines.
     *
     * @return \Ibexa\Contracts\Core\Search\Handler[]
     */
    public function getSearchEngines()
    {
        return $this->searchEngines;
    }

    /**
     * Builds search engine identified by its identifier (the "alias" attribute in the service tag),
     * resolved for current siteaccess.
     *
     * @return \Ibexa\Contracts\Core\Search\Handler
     *
     * @throws \Ibexa\Bundle\Core\ApiLoader\Exception\InvalidSearchEngine
     */
    public function buildSearchEngine()
    {
        $repositoryConfig = $this->repositoryConfigurationProvider->getRepositoryConfig();

        if (
            !(
                isset($repositoryConfig['search']['engine'])
                && isset($this->searchEngines[$repositoryConfig['search']['engine']])
            )
        ) {
            throw new InvalidSearchEngine(
                "Invalid search engine '{$repositoryConfig['search']['engine']}'. " .
                "Could not find any service tagged with 'ezplatform.search_engine' " .
                "with alias '{$repositoryConfig['search']['engine']}'."
            );
        }

        return $this->searchEngines[$repositoryConfig['search']['engine']];
    }
}

class_alias(SearchEngineFactory::class, 'eZ\Bundle\EzPublishCoreBundle\ApiLoader\SearchEngineFactory');
