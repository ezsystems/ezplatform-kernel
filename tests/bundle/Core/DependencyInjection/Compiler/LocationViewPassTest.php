<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Bundle\Core\DependencyInjection\Compiler\LocationViewPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class LocationViewPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test, just like you would do inside a bundle's load()
     * method:.
     *
     *   $container->addCompilerPass(new MyCompilerPass());
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new LocationViewPass());
    }

    public function testAddViewProvider()
    {
        $def = new Definition();
        $def->addTag(LocationViewPass::VIEW_PROVIDER_IDENTIFIER, ['priority' => 12]);
        $serviceId = 'service_id';
        $this->setDefinition($serviceId, $def);

        $this->compile();
        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            $serviceId,
            'ezpublish.view_provider',
            ['priority' => 12, 'type' => LocationViewPass::VIEW_TYPE]
        );
    }
}

class_alias(LocationViewPassTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Compiler\LocationViewPassTest');
