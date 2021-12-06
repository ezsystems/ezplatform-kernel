<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Test;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\DBAL\Connection;
use FOS\JsRoutingBundle\FOSJsRoutingBundle;
use Ibexa\Bundle\Core\IbexaCoreBundle;
use Ibexa\Bundle\LegacySearchEngine\IbexaLegacySearchEngineBundle;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository;
use JMS\TranslationBundle\JMSTranslationBundle;
use Liip\ImagineBundle\LiipImagineBundle;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @experimental
 *
 * Baseline test kernel that dependent packages can extend for their integration tests.
 *
 * ## Configuring the kernel
 *
 * It automatically exposes all Repository-based services for consumption in tests (marking them as public prevents
 * them from being removed from test container). A minimal configuration Symfony framework configuration is provided,
 * along with Doctrine connection.
 *
 * To supply a different configuration, extend IbexaTestKernel::loadConfiguration() method.
 *
 * You can supply your own services (which is something you probably want) by extending IbexaTestKernel::loadServices().
 *
 * If you need even more control over how the container is built you can do that by extending the
 * IbexaTestKernel::registerContainerConfiguration().
 *
 * ## Adding bundles
 *
 * Bundles can be added by extending IbexaTestKernel::registerBundles() method (just like in any Kernel).
 *
 * ## Exposing your services
 *
 * To add services to the test Kernel and make them available in tests via IbexaKernelTestCase::getServiceByClassName(),
 * you'll need to extend IbexaTestKernel::getExposedServicesByClass() and / or IbexaTestKernel::getExposedServicesById()
 * method.
 *
 * IbexaTestKernel::getExposedServicesByClass() is a simpler variant provided for services that are registered in
 * service container using their FQCN.
 *
 * IbexaTestKernel::getExposedServicesById() is useful if your service is not registered as it's FQCN (for example,
 * if you have multiple services for the same class / interface).
 *
 * If don't need the repository services (or not all), you can replace the IbexaTestKernel::EXPOSED_SERVICES_BY_CLASS and
 * IbexaTestKernel::EXPOSED_SERVICES_BY_ID consts in extending class, without changing the methods above.
 */
class IbexaTestKernel extends Kernel
{
    /**
     * @var iterable<class-string>
     */
    protected const EXPOSED_SERVICES_BY_CLASS = [
        TransactionHandler::class,
        Connection::class,
        Repository\Repository::class,
        Repository\ContentService::class,
        Repository\ContentTypeService::class,
        Repository\LanguageService::class,
        Repository\LocationService::class,
        Repository\ObjectStateService::class,
        Repository\PermissionResolver::class,
        Repository\RoleService::class,
        Repository\SearchService::class,
        Repository\SectionService::class,
        Repository\UserService::class,
    ];

    /**
     * @var iterable<string, class-string>
     */
    protected const EXPOSED_SERVICES_BY_ID = [];

    /**
     * @return string a service ID that service aliases will be registered as
     */
    public static function getAliasServiceId(string $id): string
    {
        return 'test.' . $id;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/ibexa-test-kernel/' . md5(serialize(getenv())) . md5(static::class);
    }

    public function getBuildDir(): string
    {
        return sys_get_temp_dir() . '/ibexa-test-kernel-build/' . md5(serialize(getenv())) . md5(static::class);
    }

    public function registerBundles(): iterable
    {
        yield new SecurityBundle();
        yield new IbexaCoreBundle();
        yield new IbexaLegacySearchEngineBundle();
        yield new JMSTranslationBundle();
        yield new FOSJsRoutingBundle();
        yield new FrameworkBundle();
        yield new LiipImagineBundle();
        yield new TwigBundle();
        yield new DoctrineBundle();
    }

    /**
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(static function (ContainerBuilder $container): void {
            $container->setParameter('ibexa.core.test.resource_dir', self::getResourcesPath());
        });

        $this->loadConfiguration($loader);
        $this->loadServices($loader);

        $loader->load(static function (ContainerBuilder $container): void {
            self::createPublicAliasesForServicesUnderTest($container);
            self::setUpTestLogger($container);
        });
    }

    /**
     * @throws \Exception
     */
    protected function loadConfiguration(LoaderInterface $loader): void
    {
        $loader->load(self::getResourcesPath() . '/config/doctrine.php');
        $loader->load(self::getResourcesPath() . '/config/ezpublish.yaml');
        $loader->load(self::getResourcesPath() . '/config/framework.yaml');
        $loader->load(self::getResourcesPath() . '/config/security.yaml');
    }

    /**
     * @throws \Exception
     */
    protected function loadServices(LoaderInterface $loader): void
    {
        $loader->load(self::getResourcesPath() . '/services/fixture-services.yaml');
    }

    /**
     * @return iterable<class-string>
     */
    protected static function getExposedServicesByClass(): iterable
    {
        return static::EXPOSED_SERVICES_BY_CLASS;
    }

    /**
     * @return iterable<string, class-string>
     */
    protected static function getExposedServicesById(): iterable
    {
        return static::EXPOSED_SERVICES_BY_ID;
    }

    private static function getResourcesPath(): string
    {
        return dirname(__DIR__, 3) . '/tests/bundle/Core/Resources';
    }

    private static function createPublicAliasesForServicesUnderTest(ContainerBuilder $container): void
    {
        foreach (static::getExposedServicesByClass() as $className) {
            $container->setAlias(static::getAliasServiceId($className), $className)
                ->setPublic(true);
        }

        foreach (static::getExposedServicesById() as $id => $className) {
            $container->setAlias(static::getAliasServiceId($id), $id)
                ->setPublic(true);
        }
    }

    private static function setUpTestLogger(ContainerBuilder $container): void
    {
        $container->setDefinition('logger', new Definition(NullLogger::class));
    }
}
