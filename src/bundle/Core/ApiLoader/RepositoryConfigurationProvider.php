<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\ApiLoader;

use Ibexa\Bundle\Core\ApiLoader\Exception\InvalidRepositoryException;
use Ibexa\Core\MVC\ConfigResolverInterface;

/**
 * The repository configuration provider.
 */
class RepositoryConfigurationProvider
{
    private const REPOSITORY_STORAGE = 'storage';
    private const REPOSITORY_CONNECTION = 'connection';
    private const DEFAULT_CONNECTION_NAME = 'default';

    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var array */
    private $repositories;

    public function __construct(ConfigResolverInterface $configResolver, array $repositories)
    {
        $this->configResolver = $configResolver;
        $this->repositories = $repositories;
    }

    /**
     * @return array
     *
     * @throws \Ibexa\Bundle\Core\ApiLoader\Exception\InvalidRepositoryException
     */
    public function getRepositoryConfig()
    {
        // Takes configured repository as the reference, if it exists.
        // If not, the first configured repository is considered instead.
        $repositoryAlias = $this->configResolver->getParameter('repository');
        $repositoryAlias = $repositoryAlias ?: $this->getDefaultRepositoryAlias();

        if (empty($repositoryAlias) || !isset($this->repositories[$repositoryAlias])) {
            throw new InvalidRepositoryException(
                "Undefined Repository '$repositoryAlias'. Check if the Repository is configured in your project's ibexa.yaml."
            );
        }

        return ['alias' => $repositoryAlias] + $this->repositories[$repositoryAlias];
    }

    public function getCurrentRepositoryAlias(): string
    {
        return $this->getRepositoryConfig()['alias'];
    }

    public function getDefaultRepositoryAlias(): ?string
    {
        $aliases = array_keys($this->repositories);

        return array_shift($aliases);
    }

    public function getStorageConnectionName(): string
    {
        $repositoryConfig = $this->getRepositoryConfig();

        return $repositoryConfig[self::REPOSITORY_STORAGE][self::REPOSITORY_CONNECTION]
            ? $repositoryConfig[self::REPOSITORY_STORAGE][self::REPOSITORY_CONNECTION]
            : self::DEFAULT_CONNECTION_NAME;
    }
}

class_alias(RepositoryConfigurationProvider::class, 'eZ\Bundle\EzPublishCoreBundle\ApiLoader\RepositoryConfigurationProvider');
