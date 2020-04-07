<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository\Tests;

use eZ\Publish\API\Repository\Tests\Container\Compiler\SetAllServicesPublicPass;
use eZ\Publish\Core\Base\Container\Compiler;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Symfony Container Builder for eZ Platform Repository.
 *
 * @internal for internal use by Repository tests
 */
final class RepositoryContainerBuilder extends ContainerBuilder
{
    /**
     * Data Source Name.
     *
     * @var string
     */
    private $dsn;

    /**
     * @throws \Exception
     */
    public function buildTestContainer(): void
    {
        $installDir = dirname(__DIR__, 5);

        $settingsPath = "{$installDir}/eZ/Publish/Core/settings/";
        $loader = new YamlFileLoader($this, new FileLocator($settingsPath));

        // @todo reorder
        $loader->load('fieldtypes.yml');
        $loader->load('io.yml');
        $loader->load('repository.yml');
        $loader->load('repository/inner.yml');
        $loader->load('repository/event.yml');
        $loader->load('repository/siteaccessaware.yml');
        $loader->load('repository/autowire.yml');
        $loader->load('roles.yml');
        $loader->load('fieldtype_external_storages.yml');
        $loader->load('storage_engines/common.yml');
        $loader->load('storage_engines/shortcuts.yml');
        $loader->load('storage_engines/legacy.yml');
        $loader->load('search_engines/legacy.yml');
        $loader->load('storage_engines/cache.yml');
        $loader->load('settings.yml');
        $loader->load('fieldtype_services.yml');
        $loader->load('utils.yml');
        $loader->load('tests/common.yml');
        $loader->load('tests/integration_legacy.yml');
        $loader->load('policies.yml');
        $loader->load('events.yml');
        $loader->load('thumbnails.yml');
        $loader->load('indexable_fieldtypes.yml');
        $loader->load('tests/override.yml');

        if (($_SERVER['CUSTOM_CACHE_POOL'] ?? null) === 'singleredis') {
            $this->registerRedisServices();
        }

        $this->setParameter('ezpublish.kernel.root_dir', $installDir);

        $this->setParameter(
            'legacy_dsn',
            $this->getDsn()
        );

        $this->addCoreCompilerPasses();
    }

    /**
     * Get data source name.
     *
     * The database connection string is read from an optional environment
     * variable "DATABASE" and defaults to an in-memory SQLite database.
     */
    protected function getDsn(): string
    {
        if (!$this->dsn) {
            $this->dsn = $_SERVER['DATABASE'] ?? null;
            if (null === $this->dsn) {
                $this->dsn = 'sqlite://:memory:';
            }
        }

        return $this->dsn;
    }

    private function registerRedisServices(): void
    {
        $this
            ->register('ezpublish.cache_pool.driver.redis', 'Redis')
            ->addMethodCall('connect', [(getenv('CACHE_HOST') ?: '127.0.0.1'), 6379, 2.5]);

        $this
            ->register('ezpublish.cache_pool.driver', RedisAdapter::class)
            ->setArguments([new Reference('ezpublish.cache_pool.driver.redis'), '', 120]);
    }

    private function addCoreCompilerPasses(): void
    {
        $this->addCompilerPass(new Compiler\FieldTypeRegistryPass(), PassConfig::TYPE_OPTIMIZE);
        $this->addCompilerPass(new Compiler\Persistence\FieldTypeRegistryPass(), PassConfig::TYPE_OPTIMIZE);

        $this->addCompilerPass(new Compiler\Storage\ExternalStorageRegistryPass());
        $this->addCompilerPass(new Compiler\Storage\Legacy\FieldValueConverterRegistryPass());
        $this->addCompilerPass(new Compiler\Storage\Legacy\RoleLimitationConverterPass());

        $this->addCompilerPass(new Compiler\Search\FieldRegistryPass());
        $this->addCompilerPass(new Compiler\Search\Legacy\CriteriaConverterPass());
        $this->addCompilerPass(new Compiler\Search\Legacy\CriterionFieldValueHandlerRegistryPass());
        $this->addCompilerPass(new Compiler\Search\Legacy\SortClauseConverterPass());

        // Symfony 4+ makes services private by default. Test cases are not prepared for this.
        // This is a simple workaround to override services as public.
        $this->addCompilerPass(new SetAllServicesPublicPass());
    }
}
