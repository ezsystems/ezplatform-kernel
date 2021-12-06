<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core;

use Ibexa\Bundle\Core\DependencyInjection\Compiler\BinaryContentDownloadPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\ChainConfigResolverPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\ChainRoutingPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\ConsoleCacheWarmupPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\ConsoleCommandPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\ContentViewPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\EntityManagerFactoryServiceLocatorPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\FieldTypeParameterProviderRegistryPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\FragmentPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\ImaginePass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\InjectEntityManagerMappingsPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\LazyDoctrineRepositoriesPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\LocationViewPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\NotificationRendererPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\PlaceholderProviderPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\QueryTypePass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\RegisterSearchEngineIndexerPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\RegisterSearchEnginePass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\RegisterStorageEnginePass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\RouterPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\SecurityPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\SessionConfigurationPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\SiteAccessMatcherRegistryPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\SlugConverterConfigurationPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\StorageConnectionPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\TranslationCollectorPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\URLHandlerPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\ViewMatcherRegistryPass;
use Ibexa\Bundle\Core\DependencyInjection\Compiler\ViewProvidersPass;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\ComplexSettings\ComplexSettingParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser as ConfigParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\Repository as RepositoryConfigParser;
use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Ibexa\Bundle\Core\DependencyInjection\Security\HttpBasicFactory;
use Ibexa\Contracts\Core\MVC\View\VariableProvider;
use Ibexa\Core\Base\Container\Compiler\FieldTypeRegistryPass;
use Ibexa\Core\Base\Container\Compiler\GenericFieldTypeConverterPass;
use Ibexa\Core\Base\Container\Compiler\Persistence\FieldTypeRegistryPass as PersistenceFieldTypeRegistryPass;
use Ibexa\Core\Base\Container\Compiler\Storage\ExternalStorageRegistryPass;
use Ibexa\Core\Base\Container\Compiler\Storage\Legacy\FieldValueConverterRegistryPass;
use Ibexa\Core\Base\Container\Compiler\Storage\Legacy\RoleLimitationConverterPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new GenericFieldTypeConverterPass(), PassConfig::TYPE_OPTIMIZE);
        $container->addCompilerPass(new FieldTypeRegistryPass(), PassConfig::TYPE_OPTIMIZE);
        $container->addCompilerPass(new PersistenceFieldTypeRegistryPass(), PassConfig::TYPE_OPTIMIZE);
        $container->addCompilerPass(new FieldTypeParameterProviderRegistryPass());
        $container->addCompilerPass(new ChainRoutingPass());
        $container->addCompilerPass(new ChainConfigResolverPass());
        $container->addCompilerPass(new RegisterStorageEnginePass());
        $container->addCompilerPass(new RegisterSearchEnginePass());
        $container->addCompilerPass(new RegisterSearchEngineIndexerPass());
        $container->addCompilerPass(new ContentViewPass());
        $container->addCompilerPass(new LocationViewPass());
        $container->addCompilerPass(new RouterPass());
        $container->addCompilerPass(new SecurityPass());
        $container->addCompilerPass(new FragmentPass());
        $container->addCompilerPass(new StorageConnectionPass());
        $container->addCompilerPass(new ImaginePass());
        $container->addCompilerPass(new URLHandlerPass());
        $container->addCompilerPass(new BinaryContentDownloadPass());
        $container->addCompilerPass(new ViewProvidersPass());
        $container->addCompilerPass(new PlaceholderProviderPass());
        $container->addCompilerPass(new NotificationRendererPass());
        $container->addCompilerPass(new ConsoleCacheWarmupPass());
        $container->addCompilerPass(new ViewMatcherRegistryPass());
        $container->addCompilerPass(new SiteAccessMatcherRegistryPass());
        $container->addCompilerPass(new ConsoleCommandPass());
        $container->addCompilerPass(new LazyDoctrineRepositoriesPass(), PassConfig::TYPE_BEFORE_REMOVING);
        $container->addCompilerPass(new EntityManagerFactoryServiceLocatorPass());
        $container->addCompilerPass(new InjectEntityManagerMappingsPass());
        $container->addCompilerPass(new SessionConfigurationPass());

        // Storage passes
        $container->addCompilerPass(new ExternalStorageRegistryPass());
        // Legacy Storage passes
        $container->addCompilerPass(new FieldValueConverterRegistryPass());
        $container->addCompilerPass(new RoleLimitationConverterPass());
        $container->addCompilerPass(new QueryTypePass());

        $securityExtension = $container->getExtension('security');
        $securityExtension->addSecurityListenerFactory(new HttpBasicFactory());
        $container->addCompilerPass(new TranslationCollectorPass());
        $container->addCompilerPass(new SlugConverterConfigurationPass());

        $container->registerForAutoconfiguration(VariableProvider::class)->addTag('ezplatform.view.variable_provider');
    }

    public function getContainerExtension()
    {
        if (!isset($this->extension)) {
            $this->extension = new IbexaCoreExtension(
                [
                    // LocationView config parser needs to be specified AFTER ContentView config
                    // parser since it is used to convert location view override rules to content
                    // view override rules. If it were specified before, ContentView provider would
                    // just undo the conversion LocationView did.
                    new ConfigParser\ContentView(),
                    new ConfigParser\LocationView(),
                    new ConfigParser\Common(),
                    new ConfigParser\Content(),
                    new ConfigParser\FieldType\ImageAsset(),
                    new ConfigParser\FieldTemplates(),
                    new ConfigParser\FieldEditTemplates(),
                    new ConfigParser\FieldDefinitionSettingsTemplates(),
                    new ConfigParser\FieldDefinitionEditTemplates(),
                    new ConfigParser\Image(),
                    new ConfigParser\Languages(),
                    new ConfigParser\IO(new ComplexSettingParser()),
                    new ConfigParser\UrlChecker(),
                    new ConfigParser\TwigVariablesParser(),
                ],
                [
                    new RepositoryConfigParser\Storage(),
                    new RepositoryConfigParser\Search(),
                    new RepositoryConfigParser\FieldGroups(),
                    new RepositoryConfigParser\Options(),
                ]
            );
        }

        return $this->extension;
    }
}

class_alias(IbexaCoreBundle::class, 'eZ\Bundle\EzPublishCoreBundle\EzPublishCoreBundle');
