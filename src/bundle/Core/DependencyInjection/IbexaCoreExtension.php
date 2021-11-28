<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Compiler\QueryTypePass;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\ParserInterface;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\RepositoryConfigParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\RepositoryConfigParserInterface;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\Suggestion\Collector\SuggestionCollector;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\Suggestion\Collector\SuggestionCollectorAwareInterface;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\Suggestion\Formatter\YamlSuggestionFormatter;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PoliciesConfigBuilder;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;
use Ibexa\Bundle\Core\SiteAccess\SiteAccessConfigurationFilter;
use Ibexa\Contracts\Core\MVC\EventSubscriber\ConfigScopeChangeSubscriber;
use Ibexa\Contracts\Core\Repository\Values\Filter\CriterionQueryBuilder as FilteringCriterionQueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Filter\SortClauseQueryBuilder as FilteringSortClauseQueryBuilder;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\QueryType\QueryType;
use InvalidArgumentException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class IbexaCoreExtension extends Extension implements PrependExtensionInterface
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

    /** @var \Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface[] */
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
        return 'ibexa';
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

        $this->configureGenericSetup($container);
        $this->configurePlatformShSetup($container);
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
     *     $ibexaExtension = $container->getExtension('ibexa');
     *     $ibexaExtension->addPolicyProvider($myPolicyProvider);
     * }
     * ```
     *
     * @since 6.0
     *
     * @param \Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface $policyProvider
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
     *     $ibexaExtension = $container->getExtension('ibexa');
     *     $ibexaExtension->addConfigParser($myConfigParser);
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
     *     $ibexaExtension = $container->getExtension('ibexa');
     *     $ibexaExtension->addDefaultSettings(
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

        $kernelConfigs = $container->getExtensionConfig('ibexa');
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
                    array_merge(...$repositoryConnections) ?? []
                )
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

    /**
     * Moved from {@see \EzSystems\EzPlatformCoreBundle\DependencyInjection\EzPlatformCoreExtension::configureGenericSetup}.
     *
     * @throws \Exception
     */
    private function configureGenericSetup(ContainerBuilder $container): void
    {
        // One of `legacy` (default) or `solr`
        $container->setParameter('search_engine', '%env(SEARCH_ENGINE)%');

        // Session save path as used by symfony session handlers (eg. used for dsn with redis)
        $container->setParameter('ezplatform.session.save_path', '%kernel.project_dir%/var/sessions/%kernel.environment%');

        // Predefined pools are located in config/packages/cache_pool/
        // You can add your own cache pool to the folder mentioned above.
        // In order to change the default cache_pool use environmental variable export.
        // The line below must not be altered as required cache service files are resolved based on environmental config.
        $container->setParameter('cache_pool', '%env(CACHE_POOL)%');

        // By default cache ttl is set to 24h, when using Varnish you can set a much higher value. High values depends on
        // using EzSystemsPlatformHttpCacheBundle (default as of v1.12) which by design expires affected cache on changes
        $container->setParameter('httpcache_default_ttl', '%env(HTTPCACHE_DEFAULT_TTL)%');

        // Settings for HttpCache
        $container->setParameter('purge_server', '%env(HTTPCACHE_PURGE_SERVER)%');

        // Identifier used to generate the CSRF token. Commenting this line will result in authentication
        // issues both in AdminUI and REST calls
        $container->setParameter('ezpublish_rest.csrf_token_intention', 'authenticate');

        // Varnish invalidation/purge token (for use on platform.sh, eZ Platform Cloud and other places you can't use IP for ACL)
        $container->setParameter('varnish_invalidate_token', '%env(resolve:default::HTTPCACHE_VARNISH_INVALIDATE_TOKEN)%');

        // Compile time handlers
        // These are defined at compile time, and hence can't be set at runtime using env()
        // config/env/generic.php takes care about letting you set them by env variables

        // Session handler, by default set to file based (instead of ~) in order to be able to use %ezplatform.session.save_path%
        $container->setParameter('ezplatform.session.handler_id', 'session.handler.native_file');

        // Purge type used by HttpCache system ("local", "varnish"/"http", and on ee also "fastly")
        $container->setParameter('purge_type', '%env(HTTPCACHE_PURGE_TYPE)%');

        $container->setParameter('solr_dsn', '%env(SOLR_DSN)%');
        $container->setParameter('solr_core', '%env(SOLR_CORE)%');

        $container->setParameter('siso_search.solr.host', '%env(SISO_SEARCH_SOLR_HOST)%');
        $container->setParameter('siso_search.solr.port', '%env(SISO_SEARCH_SOLR_PORT)%');
        $container->setParameter('siso_search.solr.core', '%env(SISO_SEARCH_SOLR_CORE)%');
        $container->setParameter('siso_search.solr.path', '%env(SISO_SEARCH_SOLR_PATH)%');

        $projectDir = $container->getParameter('kernel.project_dir');

        if ($dfsNfsPath = $_SERVER['DFS_NFS_PATH'] ?? false) {
            $container->setParameter('dfs_nfs_path', $dfsNfsPath);

            $parameterMap = [
                'dfs_database_charset' => 'database_charset',
                'dfs_database_driver' => 'database_driver',
                'dfs_database_collation' => 'database_collation',
            ];

            foreach ($parameterMap as $dfsParameter => $platformParameter) {
                $container->setParameter(
                    $dfsParameter,
                    $_SERVER[strtoupper($dfsParameter)] ?? $container->getParameter($platformParameter)
                );
            }

            $loader = new Loader\YamlFileLoader($container, new FileLocator($projectDir . '/config/packages/dfs'));
            $loader->load('dfs.yaml');
        }

        // Cache settings
        // If CACHE_POOL env variable is set, check if there is a yml file that needs to be loaded for it
        if (($pool = $_SERVER['CACHE_POOL'] ?? false) && file_exists($projectDir . "/config/packages/cache_pool/${pool}.yaml")) {
            $loader = new Loader\YamlFileLoader($container, new FileLocator($projectDir . '/config/packages/cache_pool'));
            $loader->load($pool . '.yaml');
        }

        if ($purgeType = $_SERVER['HTTPCACHE_PURGE_TYPE'] ?? false) {
            $container->setParameter('purge_type', $purgeType);
            $container->setParameter('ezpublish.http_cache.purge_type', $purgeType);
        }

        if ($value = $_SERVER['MAILER_TRANSPORT'] ?? false) {
            $container->setParameter('mailer_transport', $value);
        }

        if ($value = $_SERVER['LOG_TYPE'] ?? false) {
            $container->setParameter('log_type', $value);
        }

        if ($value = $_SERVER['SESSION_HANDLER_ID'] ?? false) {
            $container->setParameter('ezplatform.session.handler_id', $value);
        }

        if ($value = $_SERVER['SESSION_SAVE_PATH'] ?? false) {
            $container->setParameter('ezplatform.session.save_path', $value);
        }
    }

    /**
     * Moved from {@see \EzSystems\EzPlatformCoreBundle\DependencyInjection\EzPlatformCoreExtension::configurePlatformShSetup}.
     *
     * @throws \Exception
     */
    private function configurePlatformShSetup(ContainerBuilder $container): void
    {
        $projectDir = $container->getParameter('kernel.project_dir');

        // Run for all hooks, incl build step
        if ($_SERVER['PLATFORM_PROJECT_ENTROPY'] ?? false) {
            // Disable PHPStormPass as we don't have write access & it's not localhost
            $container->setParameter('ezdesign.phpstorm.enabled', false);
        }

        // Will not be executed on build step
        $relationships = $_SERVER['PLATFORM_RELATIONSHIPS'] ?? false;
        if (!$relationships) {
            return;
        }
        $routes = $_SERVER['PLATFORM_ROUTES'];

        $relationships = json_decode(base64_decode($relationships), true);
        $routes = json_decode(base64_decode($routes), true);

        // PLATFORMSH_DFS_NFS_PATH is different compared to DFS_NFS_PATH in the sense that it is relative to ezplatform dir
        // DFS_NFS_PATH is an absolute path
        if ($dfsNfsPath = $_SERVER['PLATFORMSH_DFS_NFS_PATH'] ?? false) {
            $container->setParameter('dfs_nfs_path', sprintf('%s/%s', $projectDir, $dfsNfsPath));

            // Map common parameters
            $container->setParameter('dfs_database_charset', $container->getParameter('database_charset'));
            $container->setParameter(
                'dfs_database_collation',
                $container->getParameter('database_collation')
            );
            if (\array_key_exists('dfs_database', $relationships)) {
                // process dedicated P.sh dedicated config
                foreach ($relationships['dfs_database'] as $endpoint) {
                    if (empty($endpoint['query']['is_master'])) {
                        continue;
                    }
                    $container->setParameter('dfs_database_driver', 'pdo_' . $endpoint['scheme']);
                    $container->setParameter(
                        'dfs_database_url',
                        sprintf(
                            '%s://%s:%s:%d@%s/%s',
                            $endpoint['scheme'],
                            $endpoint['username'],
                            $endpoint['password'],
                            $endpoint['port'],
                            $endpoint['host'],
                            $endpoint['path']
                        )
                    );
                }
            } else {
                // or set fallback from the Repository database, if not configured
                $container->setParameter('dfs_database_driver', $container->getParameter('database_driver'));
            }

            $loader = new Loader\YamlFileLoader($container, new FileLocator($projectDir . '/config/packages/dfs'));
            $loader->load('dfs.yaml');
        }

        // Use Redis-based caching if possible.
        if (isset($relationships['rediscache'])) {
            foreach ($relationships['rediscache'] as $endpoint) {
                if ($endpoint['scheme'] !== 'redis') {
                    continue;
                }

                $loader = new Loader\YamlFileLoader($container, new FileLocator($projectDir . '/config/packages/cache_pool'));
                $loader->load('cache.redis.yaml');

                $container->setParameter('cache_pool', 'cache.redis');
                $container->setParameter('cache_dsn', sprintf('%s:%d', $endpoint['host'], $endpoint['port']) . '?retry_interval=3');
            }
        } elseif (isset($relationships['cache'])) {
            // Fallback to memcached if here (deprecated, we will only handle redis here in the future)
            foreach ($relationships['cache'] as $endpoint) {
                if ($endpoint['scheme'] !== 'memcached') {
                    continue;
                }

                @trigger_error('Usage of Memcached is deprecated, redis is recommended', E_USER_DEPRECATED);

                $container->setParameter('cache_pool', 'cache.memcached');
                $container->setParameter('cache_dsn', sprintf('%s:%d', $endpoint['host'], $endpoint['port']));

                $loader = new Loader\YamlFileLoader($container, new FileLocator($projectDir . '/config/packages/cache_pool'));
                $loader->load('cache.memcached.yaml');
            }
        }

        // Use Redis-based sessions if possible. If a separate Redis instance
        // is available, use that.  If not, share a Redis instance with the
        // Cache.  (That should be safe to do except on especially high-traffic sites.)
        if (isset($relationships['redissession'])) {
            foreach ($relationships['redissession'] as $endpoint) {
                if ($endpoint['scheme'] !== 'redis') {
                    continue;
                }

                $container->setParameter('ezplatform.session.handler_id', 'ezplatform.core.session.handler.native_redis');
                $container->setParameter('ezplatform.session.save_path', sprintf('%s:%d', $endpoint['host'], $endpoint['port']));
            }
        } elseif (isset($relationships['rediscache'])) {
            foreach ($relationships['rediscache'] as $endpoint) {
                if ($endpoint['scheme'] !== 'redis') {
                    continue;
                }

                $container->setParameter('ezplatform.session.handler_id', 'ezplatform.core.session.handler.native_redis');
                $container->setParameter('ezplatform.session.save_path', sprintf('%s:%d', $endpoint['host'], $endpoint['port']));
            }
        }

        if (isset($relationships['solr'])) {
            foreach ($relationships['solr'] as $endpoint) {
                if ($endpoint['scheme'] !== 'solr') {
                    continue;
                }

                $container->setParameter('search_engine', 'solr');

                $container->setParameter('solr_dsn', sprintf('http://%s:%d/%s', $endpoint['host'], $endpoint['port'], 'solr'));
                // To set solr_core parameter we assume path is in form like: "solr/collection1"
                $container->setParameter('solr_core', substr($endpoint['path'], 5));

                // Ibexa DXP Commerce needs special treatment due to Solarium use
                // @todo Remove this once Commerce is fully rewritten
                $bundles = $container->getParameter('kernel.bundles');

                if (isset($bundles['IbexaCommerceSearchBundle'])) {
                    $container->setParameter('solr_dsn', sprintf('http://%s:%d', $endpoint['host'], $endpoint['port']));
                    // To set solr_core parameter we assume path is in form like: "solr/collection1"
                    $container->setParameter('solr_core', substr($endpoint['path'], 5));

                    $container->setParameter('siso_search.solr.host', $endpoint['host']);
                    $container->setParameter('siso_search.solr.port', $endpoint['port']);
                    $container->setParameter('siso_search.solr.core', $endpoint['rel']);
                }
            }
        }

        if (isset($relationships['elasticsearch'])) {
            foreach ($relationships['elasticsearch'] as $endpoint) {
                $dsn = sprintf('%s:%d', $endpoint['host'], $endpoint['port']);

                if ($endpoint['username'] !== null && $endpoint['password'] !== null) {
                    $dsn = $endpoint['username'] . ':' . $endpoint['password'] . '@' . $dsn;
                }

                if ($endpoint['path'] !== null) {
                    $dsn .= '/' . $endpoint['path'];
                }

                $dsn = $endpoint['scheme'] . '://' . $dsn;

                $container->setParameter('search_engine', 'elasticsearch');
                $container->setParameter('elasticsearch_dsn', $dsn);
            }
        }

        // We will pick a varnish route by the following prioritization:
        // - The first route found that has upstream: varnish
        // - if primary route has upstream: varnish, that route will be prioritised
        // If no route is found with upstream: varnish, then purge_server will not be set
        $route = null;
        foreach ($routes as $host => $info) {
            if ($route === null && $info['type'] === 'upstream' && $info['upstream'] === 'varnish') {
                $route = $host;
            }
            if ($info['type'] === 'upstream' && $info['upstream'] === 'varnish' && $info['primary'] === true) {
                $route = $host;
                break;
            }
        }

        if ($route !== null && !($_SERVER['SKIP_HTTPCACHE_PURGE'] ?? false)) {
            $purgeServer = rtrim($route, '/');
            if (($_SERVER['HTTPCACHE_USERNAME'] ?? false) && ($_SERVER['HTTPCACHE_PASSWORD'] ?? false)) {
                $domain = parse_url($purgeServer, PHP_URL_HOST);
                $credentials = urlencode($_SERVER['HTTPCACHE_USERNAME']) . ':' . urlencode($_SERVER['HTTPCACHE_PASSWORD']);
                $purgeServer = str_replace($domain, $credentials . '@' . $domain, $purgeServer);
            }

            $container->setParameter('ezpublish.http_cache.purge_type', 'varnish');
            $container->setParameter('purge_type', 'varnish');
            $container->setParameter('purge_server', $purgeServer);
        }
        // Setting default value for HTTPCACHE_VARNISH_INVALIDATE_TOKEN if it is not explicitly set
        if (!($_SERVER['HTTPCACHE_VARNISH_INVALIDATE_TOKEN'] ?? false)) {
            $container->setParameter('varnish_invalidate_token', $_SERVER['PLATFORM_PROJECT_ENTROPY']);
        }

        // Adapt config based on enabled PHP extensions
        // Get imagine to use imagick if enabled, to avoid using php memory for image conversions
        if (\extension_loaded('imagick')) {
            $container->setParameter('liip_imagine_driver', 'imagick');
        }
    }
}

class_alias(IbexaCoreExtension::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension');
