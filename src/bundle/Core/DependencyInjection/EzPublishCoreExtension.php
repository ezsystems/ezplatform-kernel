<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Compiler\QueryTypePass;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\Suggestion\Collector\SuggestionCollector;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\Suggestion\Collector\SuggestionCollectorAwareInterface;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\Suggestion\Formatter\YamlSuggestionFormatter;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PoliciesConfigBuilder;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;
use Ibexa\Bundle\Core\SiteAccess\SiteAccessConfigurationFilter;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\QueryType\QueryType;
use Ibexa\Contracts\Core\MVC\EventSubscriber\ConfigScopeChangeSubscriber;
use Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder as FilteringCriterionQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\SortClauseQueryBuilder as FilteringSortClauseQueryBuilder;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\RepositoryConfigParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\RepositoryConfigParserInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\Config\FileLocator;
use InvalidArgumentException;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\ParserInterface;

class EzPublishCoreExtension extends Extension implements PrependExtensionInterface
{
    private const ENTITY_MANAGER_TEMPLATE = [
        'connection' => null,
        'mappings' => [],
    ];

    /** @var \Ibexa\Bundle\Core\DependencyInjection\Configuration\Suggestion\Collector\SuggestionCollector */
    private $suggestionCollector;

    /** @var \Ibexa\Bundle\Core\DependencyInjection\Configuration\ParserInterface */
    private $mainConfigParser;

    /** @var \Ibexa\Bundle\Core\DependencyInjection\Configuration\RepositoryConfigParser */
    private $mainRepositoryConfigParser;

    /** @var \Ibexa\Bundle\Core\DependencyInjection\Configuration\ParserInterface[] */
    private $siteAccessConfigParsers;

    /** @var \Ibexa\Bundle\Core\DependencyInjection\Configuration\RepositoryConfigParserInterface[] */
    private $repositoryConfigParsers = [];

    /** @var PolicyProviderInterface[] */
    private $policyProviders = [];

    /**
     * Holds a collection of YAML files, as an array with directory path as a
     * key to the array of contained file names.
     *
     * @var array
     */
    private $defaultSettingsCollection = [];

    /** @var \Ibexa\Bundle\Core\SiteAccess\SiteAccessConfigurationFilter[] */
    private $siteaccessConfigurationFilters = [];

    public function __construct(array $siteAccessConfigParsers = [], array $repositoryConfigParsers = [])
    {
        $this->siteAccessConfigParsers = $siteAccessConfigParsers;
        $this->repositoryConfigParsers = $repositoryConfigParsers;
        $this->suggestionCollector = new SuggestionCollector();
    }

    public function getAlias()
    {
        return 'ezpublish';
    }

    /**
     * Loads a specific configuration.
     *
     * @param mixed[] $configs An array of configuration values
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $configuration = $this->getConfiguration($configs, $container);

        $environment = $container->getParameter('kernel.environment');
        if (in_array($environment, ['behat', 'test'])) {
            $loader->load('feature_contexts.yml');
        }

        // Note: this is where the transformation occurs
        $config = $this->processConfiguration($configuration, $configs);

        // Base services and services overrides
        $loader->load('services.yml');
        // Security services
        $loader->load('security.yml');
        // HTTP Kernel
        $loader->load('http_kernel.yml');

        if (interface_exists('FOS\JsRoutingBundle\Extractor\ExposedRoutesExtractorInterface')) {
            $loader->load('routing/js_routing.yml');
        }

        // Default settings
        $this->handleDefaultSettingsLoading($container, $loader);

        $this->registerRepositoriesConfiguration($config, $container);
        $this->registerSiteAccessConfiguration($config, $container);
        $this->registerImageMagickConfiguration($config, $container);
        $this->registerUrlAliasConfiguration($config, $container);
        $this->registerUrlWildcardsConfiguration($config, $container);
        $this->registerOrmConfiguration($config, $container);

        // Routing
        $this->handleRouting($config, $container, $loader);
        // Public API loading
        $this->handleApiLoading($container, $loader);
        $this->handleTemplating($container, $loader);
        $this->handleSessionLoading($container, $loader);
        $this->handleCache($config, $container, $loader);
        $this->handleLocale($config, $container, $loader);
        $this->handleHelpers($config, $container, $loader);
        $this->handleImage($config, $container, $loader);
        $this->handleUrlChecker($config, $container, $loader);
        $this->handleUrlWildcards($config, $container, $loader);

        // Map settings
        $processor = new ConfigurationProcessor($container, 'ezsettings');
        $processor->mapConfig($config, $this->getMainConfigParser());

        if ($this->suggestionCollector->hasSuggestions()) {
            $message = '';
            $suggestionFormatter = new YamlSuggestionFormatter();
            foreach ($this->suggestionCollector->getSuggestions() as $suggestion) {
                $message .= $suggestionFormatter->format($suggestion) . "\n\n";
            }

            throw new InvalidArgumentException($message);
        }

        $this->buildPolicyMap($container);

        $this->registerForAutoConfiguration($container);
    }

    /**
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return \Ibexa\Bundle\Core\DependencyInjection\Configuration
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        $configuration = new Configuration(
            $this->getMainConfigParser(),
            $this->getMainRepositoryConfigParser(),
            $this->suggestionCollector
        );

        $configuration->setSiteAccessConfigurationFilters($this->siteaccessConfigurationFilters);

        return $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->prependTranslatorConfiguration($container);
        $this->prependDoctrineConfiguration($container);
    }

    /**
     * @return \Ibexa\Bundle\Core\DependencyInjection\Configuration\ParserInterface
     */
    private function getMainConfigParser()
    {
        if ($this->mainConfigParser === null) {
            foreach ($this->siteAccessConfigParsers as $parser) {
                if ($parser instanceof SuggestionCollectorAwareInterface) {
                    $parser->setSuggestionCollector($this->suggestionCollector);
                }
            }

            $this->mainConfigParser = new ConfigParser($this->siteAccessConfigParsers);
        }

        return $this->mainConfigParser;
    }

    private function getMainRepositoryConfigParser(): RepositoryConfigParserInterface
    {
        if (!isset($this->mainRepositoryConfigParser)) {
            foreach ($this->repositoryConfigParsers as $parser) {
                if ($parser instanceof SuggestionCollectorAwareInterface) {
                    $parser->setSuggestionCollector($this->suggestionCollector);
                }
            }

            $this->mainRepositoryConfigParser = new RepositoryConfigParser($this->repositoryConfigParsers);
        }

        return $this->mainRepositoryConfigParser;
    }

    /**
     * Handle default settings.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Loader\FileLoader $loader
     *
     * @throws \Exception
     */
    private function handleDefaultSettingsLoading(ContainerBuilder $container, FileLoader $loader)
    {
        $loader->load('default_settings.yml');

        foreach ($this->defaultSettingsCollection as $fileLocation => $files) {
            $externalLoader = new Loader\YamlFileLoader($container, new FileLocator($fileLocation));
            foreach ($files as $file) {
                $externalLoader->load($file);
            }
        }
    }

    private function registerRepositoriesConfiguration(array $config, ContainerBuilder $container)
    {
        if (!isset($config['repositories'])) {
            $config['repositories'] = [];
        }

        foreach ($config['repositories'] as $name => &$repository) {
            if (empty($repository['fields_groups']['list'])) {
                $repository['fields_groups']['list'] = $container->getParameter('ezsettings.default.content.field_groups.list');
            }
        }

        $container->setParameter('ezpublish.repositories', $config['repositories']);
    }

    private function registerSiteAccessConfiguration(array $config, ContainerBuilder $container)
    {
        if (!isset($config['siteaccess'])) {
            $config['siteaccess'] = [];
            $config['siteaccess']['list'] = ['setup'];
            $config['siteaccess']['default_siteaccess'] = 'setup';
            $config['siteaccess']['groups'] = [];
            $config['siteaccess']['match'] = null;
        }

        $container->setParameter('ezpublish.siteaccess.list', $config['siteaccess']['list']);
        ConfigurationProcessor::setAvailableSiteAccesses($config['siteaccess']['list']);
        $container->setParameter('ezpublish.siteaccess.default', $config['siteaccess']['default_siteaccess']);
        $container->setParameter('ezpublish.siteaccess.match_config', $config['siteaccess']['match']);

        // Register siteaccess groups + reverse
        $container->setParameter('ezpublish.siteaccess.groups', $config['siteaccess']['groups']);
        ConfigurationProcessor::setAvailableSiteAccessGroups($config['siteaccess']['groups']);
        $groupsBySiteaccess = [];
        foreach ($config['siteaccess']['groups'] as $groupName => $groupMembers) {
            foreach ($groupMembers as $member) {
                if (!isset($groupsBySiteaccess[$member])) {
                    $groupsBySiteaccess[$member] = [];
                }

                $groupsBySiteaccess[$member][] = $groupName;
            }
        }
        $container->setParameter('ezpublish.siteaccess.groups_by_siteaccess', $groupsBySiteaccess);
        ConfigurationProcessor::setGroupsBySiteAccess($groupsBySiteaccess);
    }

    private function registerImageMagickConfiguration(array $config, ContainerBuilder $container)
    {
        if (isset($config['imagemagick'])) {
            $container->setParameter('ezpublish.image.imagemagick.enabled', $config['imagemagick']['enabled']);
            if ($config['imagemagick']['enabled']) {
                $container->setParameter('ezpublish.image.imagemagick.executable_path', dirname($config['imagemagick']['path']));
                $container->setParameter('ezpublish.image.imagemagick.executable', basename($config['imagemagick']['path']));
            }
        }

        $filters = isset($config['imagemagick']['filters']) ? $config['imagemagick']['filters'] : [];
        $filters = $filters + $container->getParameter('ezpublish.image.imagemagick.filters');
        $container->setParameter('ezpublish.image.imagemagick.filters', $filters);
    }

    private function registerOrmConfiguration(array $config, ContainerBuilder $container): void
    {
        if (!isset($config['orm']['entity_mappings'])) {
            return;
        }

        $entityMappings = $config['orm']['entity_mappings'];
        $container->setParameter('ibexa.orm.entity_mappings', $entityMappings);
    }

    /**
     * Handle routing parameters.
     *
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Loader\FileLoader $loader
     */
    private function handleRouting(array $config, ContainerBuilder $container, FileLoader $loader)
    {
        $loader->load('routing.yml');
        $container->setAlias('router', 'ezpublish.chain_router');
        $container->getAlias('router')->setPublic(true);

        if (isset($config['router']['default_router']['non_siteaccess_aware_routes'])) {
            $container->setParameter(
                'ezpublish.default_router.non_siteaccess_aware_routes',
                array_merge(
                    $container->getParameter('ezpublish.default_router.non_siteaccess_aware_routes'),
                    $config['router']['default_router']['non_siteaccess_aware_routes']
                )
            );
        }
    }

    /**
     * Handle public API loading.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Loader\FileLoader $loader
     *
     * @throws \Exception
     */
    private function handleApiLoading(ContainerBuilder $container, FileLoader $loader): void
    {
        // @todo move settings to Core Bundle Resources
        // Loading configuration from ./src/lib/Resources/settings
        $coreLoader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../../lib/Resources/settings')
        );
        $coreLoader->load('repository.yml');
        $coreLoader->load('repository/inner.yml');
        $coreLoader->load('repository/event.yml');
        $coreLoader->load('repository/siteaccessaware.yml');
        $coreLoader->load('repository/autowire.yml');
        $coreLoader->load('fieldtype_external_storages.yml');
        $coreLoader->load('fieldtypes.yml');
        $coreLoader->load('indexable_fieldtypes.yml');
        $coreLoader->load('fieldtype_services.yml');
        $coreLoader->load('roles.yml');
        $coreLoader->load('storage_engines/common.yml');
        $coreLoader->load('storage_engines/cache.yml');
        $coreLoader->load('storage_engines/legacy.yml');
        $coreLoader->load('storage_engines/shortcuts.yml');
        $coreLoader->load('search_engines/common.yml');
        $coreLoader->load('utils.yml');
        $coreLoader->load('io.yml');
        $coreLoader->load('policies.yml');
        $coreLoader->load('notification.yml');
        $coreLoader->load('user_preference.yml');
        $coreLoader->load('events.yml');
        $coreLoader->load('thumbnails.yml');
        $coreLoader->load('content_location_mapper.yml');

        // Public API services
        $loader->load('papi.yml');

        // Built-in field types
        $loader->load('fieldtype_services.yml');

        // Storage engine
        $loader->load('storage_engines.yml');

        $loader->load('query_types.yml');
        $loader->load('sort_spec.yml');
    }

    /**
     * Handle templating parameters.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Loader\FileLoader $loader
     */
    private function handleTemplating(ContainerBuilder $container, FileLoader $loader)
    {
        $loader->load('templating.yml');
    }

    /**
     * Handle session parameters.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Loader\FileLoader $loader
     */
    private function handleSessionLoading(ContainerBuilder $container, FileLoader $loader)
    {
        $loader->load('session.yml');
    }

    /**
     * Handle cache parameters.
     *
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Loader\FileLoader $loader
     *
     * @throws \InvalidArgumentException
     */
    private function handleCache(array $config, ContainerBuilder $container, FileLoader $loader)
    {
        $loader->load('cache.yml');

        if (isset($config['http_cache']['purge_type'])) {
            // resolves ENV variable at compile time, needed by ezplatform-http-cache to setup purge driver
            $purgeType = $container->resolveEnvPlaceholders($config['http_cache']['purge_type'], true);

            $container->setParameter('ezpublish.http_cache.purge_type', $purgeType);
        }
    }

    /**
     * Handle locale parameters.
     *
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Loader\FileLoader $loader
     */
    private function handleLocale(array $config, ContainerBuilder $container, FileLoader $loader)
    {
        $loader->load('locale.yml');
        $container->setParameter(
            'ezpublish.locale.conversion_map',
            $config['locale_conversion'] + $container->getParameter('ezpublish.locale.conversion_map')
        );
    }

    /**
     * Handle helpers.
     *
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Loader\FileLoader $loader
     */
    private function handleHelpers(array $config, ContainerBuilder $container, FileLoader $loader)
    {
        $loader->load('helpers.yml');
    }

    /**
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Loader\FileLoader $loader
     */
    private function handleImage(array $config, ContainerBuilder $container, FileLoader $loader)
    {
        $loader->load('image.yml');

        $providers = [];
        if (isset($config['image_placeholder'])) {
            foreach ($config['image_placeholder'] as $name => $value) {
                if (isset($providers[$name])) {
                    throw new InvalidConfigurationException("An image_placeholder called $name already exists");
                }

                $providers[$name] = $value;
            }
        }

        $container->setParameter('image_alias.placeholder_providers', $providers);
    }

    private function handleUrlChecker($config, ContainerBuilder $container, FileLoader $loader)
    {
        $loader->load('url_checker.yml');
    }

    private function buildPolicyMap(ContainerBuilder $container)
    {
        $policiesBuilder = new PoliciesConfigBuilder($container);
        foreach ($this->policyProviders as $provider) {
            $provider->addPolicies($policiesBuilder);
        }
    }

    /**
     * Adds a new policy provider to the internal collection.
     * One can call this method from a bundle `build()` method.
     *
     * ```php
     * public function build(ContainerBuilder $container)
     * {
     *     $ezExtension = $container->getExtension('ezpublish');
     *     $ezExtension->addPolicyProvider($myPolicyProvider);
     * }
     * ```
     *
     * @since 6.0
     *
     * @param PolicyProviderInterface $policyProvider
     */
    public function addPolicyProvider(PolicyProviderInterface $policyProvider)
    {
        $this->policyProviders[] = $policyProvider;
    }

    /**
     * Adds a new config parser to the internal collection.
     * One can call this method from a bundle `build()` method.
     *
     * ```php
     * public function build(ContainerBuilder $container)
     * {
     *     $ezExtension = $container->getExtension('ezpublish');
     *     $ezExtension->addConfigParser($myConfigParser);
     * }
     * ```
     *
     * @since 6.0
     *
     * @param \Ibexa\Bundle\Core\DependencyInjection\Configuration\ParserInterface $configParser
     */
    public function addConfigParser(ParserInterface $configParser)
    {
        $this->siteAccessConfigParsers[] = $configParser;
    }

    public function addRepositoryConfigParser(RepositoryConfigParserInterface $configParser): void
    {
        $this->repositoryConfigParsers[] = $configParser;
    }

    /**
     * Adds new default settings to the internal collection.
     * One can call this method from a bundle `build()` method.
     *
     * ```php
     * public function build(ContainerBuilder $container)
     * {
     *     $ezExtension = $container->getExtension('ezpublish');
     *     $ezExtension->addDefaultSettings(
     *         __DIR__ . '/Resources/config',
     *         ['default_settings.yml']
     *     );
     * }
     * ```
     *
     * @since 6.0
     *
     * @param string $fileLocation
     * @param array $files
     */
    public function addDefaultSettings($fileLocation, array $files)
    {
        $this->defaultSettingsCollection[$fileLocation] = $files;
    }

    public function addSiteAccessConfigurationFilter(SiteAccessConfigurationFilter $filter)
    {
        $this->siteaccessConfigurationFilters[] = $filter;
    }

    /**
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function registerUrlAliasConfiguration(array $config, ContainerBuilder $container)
    {
        if (!isset($config['url_alias'])) {
            $config['url_alias'] = ['slug_converter' => []];
        }

        $container->setParameter('ezpublish.url_alias.slug_converter', $config['url_alias']['slug_converter']);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function prependTranslatorConfiguration(ContainerBuilder $container)
    {
        if (!$container->hasExtension('framework')) {
            return;
        }

        $fileSystem = new Filesystem();
        $translationsPath = $container->getParameterBag()->get('kernel.project_dir') . '/vendor/ezplatform-i18n';

        if ($fileSystem->exists($translationsPath)) {
            $container->prependExtensionConfig('framework', ['translator' => ['paths' => [$translationsPath]]]);
        }
    }

    private function prependDoctrineConfiguration(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('doctrine')) {
            return;
        }

        $kernelConfigs = array_merge(
            $container->getExtensionConfig('ezpublish'),
            $container->getExtensionConfig('ezplatform')
        );
        $entityMappings = [];

        $repositoryConnections = [];
        foreach ($kernelConfigs as $config) {
            if (isset($config['orm']['entity_mappings'])) {
                $entityMappings[] = $config['orm']['entity_mappings'];
            }

            if (isset($config['repositories'])) {
                $repositoryConnections[] = array_map(
                    static function (array $repository): ?string {
                        return $repository['storage']['connection']
                            ?? 'default';
                    },
                    $config['repositories']
                );
            }
        }

        // compose clean array with all connection identifiers
        $connections = array_values(
            array_filter(
                array_unique(
                    array_merge(...$repositoryConnections) ?? [])
            )
        );

        $doctrineConfig = [
            'orm' => [
                'entity_managers' => [],
            ],
        ];

        $entityMappingConfig = !empty($entityMappings) ? array_merge_recursive(...$entityMappings) : [];

        foreach ($connections as $connection) {
            $doctrineConfig['orm']['entity_managers'][sprintf('ibexa_%s', $connection)] = array_merge(
                self::ENTITY_MANAGER_TEMPLATE,
                ['connection' => $connection, 'mappings' => $entityMappingConfig]
            );
        }

        $container->prependExtensionConfig('doctrine', $doctrineConfig);
    }

    /**
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function registerUrlWildcardsConfiguration(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('ezpublish.url_wildcards.enabled', $config['url_wildcards']['enabled'] ?? false);
    }

    /**
     * Loads configuration for UrlWildcardsRouter service if enabled.
     *
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Loader\FileLoader $loader
     */
    private function handleUrlWildcards(array $config, ContainerBuilder $container, Loader\YamlFileLoader $loader)
    {
        if ($container->getParameter('ezpublish.url_wildcards.enabled')) {
            $loader->load('url_wildcard.yml');
        }
    }

    private function registerForAutoConfiguration(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(QueryType::class)
            ->addTag(QueryTypePass::QUERY_TYPE_SERVICE_TAG);

        $container->registerForAutoconfiguration(ConfigScopeChangeSubscriber::class)
            ->addTag(
                'kernel.event_listener',
                ['method' => 'onConfigScopeChange', 'event' => MVCEvents::CONFIG_SCOPE_CHANGE]
            )
            ->addTag(
                'kernel.event_listener',
                ['method' => 'onConfigScopeChange', 'event' => MVCEvents::CONFIG_SCOPE_RESTORE]
            );

        $container->registerForAutoconfiguration(FilteringCriterionQueryBuilder::class)
            ->addTag(ServiceTags::FILTERING_CRITERION_QUERY_BUILDER);

        $container->registerForAutoconfiguration(FilteringSortClauseQueryBuilder::class)
            ->addTag(ServiceTags::FILTERING_SORT_CLAUSE_QUERY_BUILDER);
    }
}

class_alias(EzPublishCoreExtension::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension');
