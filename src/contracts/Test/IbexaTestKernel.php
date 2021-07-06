<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Test;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\DBAL\Connection;
use eZ\Bundle\EzPublishCoreBundle\EzPublishCoreBundle;
use eZ\Bundle\EzPublishLegacySearchEngineBundle\EzPublishLegacySearchEngineBundle;
use eZ\Publish\API\Repository;
use eZ\Publish\SPI\Persistence\TransactionHandler;
use eZ\Publish\SPI\Tests\Persistence\FixtureImporter;
use EzSystems\DoctrineSchema\Database\DbPlatform\SqliteDbPlatform;
use EzSystems\EzPlatformCoreBundle\EzPlatformCoreBundle;
use FOS\JsRoutingBundle\FOSJsRoutingBundle;
use JMS\TranslationBundle\JMSTranslationBundle;
use Liip\ImagineBundle\LiipImagineBundle;
use Psr\Log\Test\TestLogger;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel;

final class IbexaTestKernel extends Kernel
{
    /**
     * @var array<string, string>
     */
    public const SERVICE_ID_TO_TEST_SERVICE_MAP = [
        TransactionHandler::class => 'test.' . TransactionHandler::class,
        Connection::class => 'test.doctrine.connection',
        Repository\Repository::class => 'test.ibexa.repository',
        Repository\ContentService::class => 'test.ibexa.content_service',
        Repository\ContentTypeService::class => 'test.ibexa.content_type_service',
        Repository\LanguageService::class => 'test.ibexa.language_service',
        Repository\LocationService::class => 'test.ibexa.location_service',
        Repository\ObjectStateService::class => 'test.ibexa.object_state_service',
        Repository\PermissionResolver::class => 'test.ibexa.permission_resolver',
        Repository\RoleService::class => 'test.ibexa.role_service',
        Repository\SearchService::class => 'test.ibexa.search_service',
        Repository\SectionService::class => 'test.ibexa.section_service',
        Repository\UserService::class => 'test.ibexa.user_service',
    ];

    public function registerBundles()
    {
        return [
            new SecurityBundle(),
            new EzPublishCoreBundle(),
            new EzPlatformCoreBundle(),
            new EzPublishLegacySearchEngineBundle(),
            new JMSTranslationBundle(),
            new FOSJsRoutingBundle(),
            new FrameworkBundle(),
            new LiipImagineBundle(),
            new TwigBundle(),
            new DoctrineBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(static function (ContainerBuilder $container): void {
            self::prepareEzPlatformFramework($container);
            self::prepareDatabaseConnection($container);
            self::createPublicAliasesForServicesUnderTest($container);
            self::setUpTestLogger($container);
        });
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir() . '/ibexa-test-kernel/' . md5(get_class($this));
    }

    private static function prepareDatabaseConnection(ContainerBuilder $container): void
    {
        $container->loadFromExtension('doctrine', [
            'dbal' => [
                'memory' => true,
                'driver' => 'pdo_sqlite',
                'platform_service' => SqliteDbPlatform::class,
                'logging' => false,
            ],
        ]);

        $definition = new Definition(Repository\Tests\LegacySchemaImporter::class);
        $definition->setPublic(true);
        $definition->setArgument(0, new Reference('doctrine.dbal.default_connection'));
        $container->setDefinition('test.ibexa.migrations.schema_importer', $definition);

        $definition = new Definition(FixtureImporter::class);
        $definition->setPublic(true);
        $definition->setArgument(0, new Reference('doctrine.dbal.default_connection'));
        $container->setDefinition('test.ibexa.migrations.fixture_importer', $definition);

        $definition = new Definition(SqliteDbPlatform::class);
        $definition->addMethodCall('setEventManager', [
            new Reference('doctrine.dbal.default_connection.event_manager'),
        ]);
        $container->setDefinition(SqliteDbPlatform::class, $definition);
    }

    private static function prepareEzPlatformFramework(ContainerBuilder $container): void
    {
        $container->setParameter('io_root_dir', '');
        $container->setParameter('kernel.secret', 'foobar');
        $container->loadFromExtension('ezplatform', [
            'siteaccess' => [
                'default_siteaccess' => '__default_site_access__',
                'list' => [
                    'default' => '__default_site_access__',
                    'second' => '__second_site_access__',
                    'ger' => 'ger',
                    'eng' => 'eng',
                    'ku6"H' => 'ku6"H',
                ],
                'match' => null,
            ],
            'repositories' => [
                'default' => [
                    'storage' => null,
                    'search' => [
                        'engine' => 'legacy',
                        'connection' => 'default',
                    ],
                ],
            ],
        ]);

        $container->loadFromExtension('security', [
            'providers' => [
                'default' => [
                    'id' => 'ezpublish.security.user_provider',
                ],
            ],
            'firewalls' => [
                'main' => [
                    'anonymous' => null,
                ],
            ],
        ]);

        $container->loadFromExtension('framework', [
            'test' => true,
            'session' => [
                'storage_id' => 'session.storage.mock_file',
            ],
            'cache' => [
                'app' => 'cache.adapter.array',
            ],
            'router' => [
                'resource' => 'foo',
            ],
        ]);

        $container->setAlias(
            'eZ\Publish\Core\MVC\ConfigResolverInterface',
            'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ChainConfigResolver'
        );
    }

    private static function createPublicAliasesForServicesUnderTest(ContainerBuilder $container): void
    {
        foreach (self::SERVICE_ID_TO_TEST_SERVICE_MAP as $className => $serviceName) {
            $container->setAlias($serviceName, $className)
                ->setPublic(true);
        }
    }

    private static function setUpTestLogger(ContainerBuilder $container): void
    {
        $container->setDefinition('logger', new Definition(TestLogger::class));
    }
}
