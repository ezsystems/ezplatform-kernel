<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\ApiLoader;

use Ibexa\Contracts\Core\Persistence\Filter\Content\Handler as ContentFilteringHandler;
use Ibexa\Contracts\Core\Persistence\Filter\Location\Handler as LocationFilteringHandler;
use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Repository\PasswordHashService;
use Ibexa\Contracts\Core\Repository\PermissionService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Validator\ContentValidator;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Ibexa\Core\FieldType\FieldTypeRegistry;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\Repository\Helper\RelationProcessor;
use Ibexa\Core\Repository\Mapper;
use Ibexa\Core\Repository\Permission\LimitationService;
use Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperFactoryInterface;
use Ibexa\Core\Repository\User\PasswordValidatorInterface;
use Ibexa\Core\Search\Common\BackgroundIndexer;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class RepositoryFactory implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var string */
    private $repositoryClass;

    /**
     * Map of system configured policies.
     *
     * @var array
     */
    private $policyMap;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \Ibexa\Contracts\Core\Repository\LanguageResolver */
    private $languageResolver;

    public function __construct(
        ConfigResolverInterface $configResolver,
        $repositoryClass,
        array $policyMap,
        LanguageResolver $languageResolver,
        LoggerInterface $logger = null
    ) {
        $this->configResolver = $configResolver;
        $this->repositoryClass = $repositoryClass;
        $this->policyMap = $policyMap;
        $this->languageResolver = $languageResolver;
        $this->logger = null !== $logger ? $logger : new NullLogger();
    }

    /**
     * Builds the main repository, heart of eZ Publish API.
     *
     * This always returns the true inner Repository, please depend on ezpublish.api.repository and not this method
     * directly to make sure you get an instance wrapped inside Event / Cache / * functionality.
     */
    public function buildRepository(
        PersistenceHandler $persistenceHandler,
        SearchHandler $searchHandler,
        BackgroundIndexer $backgroundIndexer,
        RelationProcessor $relationProcessor,
        FieldTypeRegistry $fieldTypeRegistry,
        PasswordHashService $passwordHashService,
        ThumbnailStrategy $thumbnailStrategy,
        ProxyDomainMapperFactoryInterface $proxyDomainMapperFactory,
        Mapper\ContentDomainMapper $contentDomainMapper,
        Mapper\ContentTypeDomainMapper $contentTypeDomainMapper,
        Mapper\RoleDomainMapper $roleDomainMapper,
        Mapper\ContentMapper $contentMapper,
        ContentValidator $contentValidator,
        LimitationService $limitationService,
        PermissionService $permissionService,
        ContentFilteringHandler $contentFilteringHandler,
        LocationFilteringHandler $locationFilteringHandler,
        PasswordValidatorInterface $passwordValidator
    ): Repository {
        $config = $this->container->get('ezpublish.api.repository_configuration_provider')->getRepositoryConfig();

        return new $this->repositoryClass(
            $persistenceHandler,
            $searchHandler,
            $backgroundIndexer,
            $relationProcessor,
            $fieldTypeRegistry,
            $passwordHashService,
            $thumbnailStrategy,
            $proxyDomainMapperFactory,
            $contentDomainMapper,
            $contentTypeDomainMapper,
            $roleDomainMapper,
            $contentMapper,
            $contentValidator,
            $limitationService,
            $this->languageResolver,
            $permissionService,
            $contentFilteringHandler,
            $locationFilteringHandler,
            $passwordValidator,
            [
                'role' => [
                    'policyMap' => $this->policyMap,
                ],
                'languages' => $this->configResolver->getParameter('languages'),
                'content' => [
                    'default_version_archive_limit' => $config['options']['default_version_archive_limit'],
                    'remove_archived_versions_on_publish' => $config['options']['remove_archived_versions_on_publish'],
                ],
            ],
            $this->logger
        );
    }
}

class_alias(RepositoryFactory::class, 'eZ\Bundle\EzPublishCoreBundle\ApiLoader\RepositoryFactory');
