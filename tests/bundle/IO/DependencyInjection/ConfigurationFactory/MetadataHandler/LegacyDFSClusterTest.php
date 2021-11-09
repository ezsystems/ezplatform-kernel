<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\IO\DependencyInjection\ConfigurationFactory\MetadataHandler;

use Ibexa\Bundle\IO\DependencyInjection\ConfigurationFactory\MetadataHandler\LegacyDFSCluster;
use Ibexa\Tests\Bundle\IO\DependencyInjection\ConfigurationFactoryTest;
use Symfony\Component\DependencyInjection\Definition;

class LegacyDFSClusterTest extends ConfigurationFactoryTest
{
    /**
     * Returns an instance of the tested factory.
     *
     * @return \Ibexa\Bundle\IO\DependencyInjection\ConfigurationFactory
     */
    public function provideTestedFactory()
    {
        return new LegacyDFSCluster();
    }

    public function provideExpectedParentServiceId()
    {
        return 'ezpublish.core.io.metadata_handler.legacy_dfs_cluster';
    }

    public function provideParentServiceDefinition()
    {
        return new Definition(null, [null]);
    }

    public function provideHandlerConfiguration()
    {
        return ['connection' => 'doctrine.dbal.test_connection'];
    }

    /**
     * Lets you test the handler definition after it was configured.
     *
     * Use the assertContainer* methods from matthiasnoback/SymfonyDependencyInjectionTest.
     *
     * @param string $handlerServiceId id of the service that was registered by the compiler pass
     */
    public function validateConfiguredHandler($handlerServiceId)
    {
        self::assertContainerBuilderHasServiceDefinitionWithArgument(
            $handlerServiceId,
            0,
            'doctrine.dbal.test_connection'
        );
    }
}

class_alias(LegacyDFSClusterTest::class, 'eZ\Bundle\EzPublishIOBundle\Tests\DependencyInjection\ConfigurationFactory\MetadataHandler\LegacyDFSClusterTest');
