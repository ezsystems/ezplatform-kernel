<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Bundle\Core\DependencyInjection\Compiler\ChainRoutingPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers \Ibexa\Bundle\Core\DependencyInjection\Compiler\ChainRoutingPass
 */
class ChainRoutingPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDefinition('ezpublish.chain_router', new Definition());
    }

    /**
     * Register the compiler pass under test, just like you would do inside a bundle's load()
     * method:.
     *
     *   $container->addCompilerPass(new MyCompilerPass());
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ChainRoutingPass());
    }

    /**
     * @param int|null $declaredPriority
     * @param int $expectedPriority
     *
     * @dataProvider addRouterProvider
     */
    public function testAddRouter($declaredPriority, $expectedPriority)
    {
        $resolverDef = new Definition();
        $serviceId = 'some_service_id';
        if ($declaredPriority !== null) {
            $resolverDef->addTag('router', ['priority' => $declaredPriority]);
        } else {
            $resolverDef->addTag('router');
        }

        $this->setDefinition($serviceId, $resolverDef);
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ezpublish.chain_router',
            'add',
            [new Reference($serviceId), $expectedPriority]
        );
    }

    /**
     * @param int|null $declaredPriority
     * @param int $expectedPriority
     *
     * @dataProvider addRouterProvider
     */
    public function testAddRouterWithDefaultRouter($declaredPriority, $expectedPriority)
    {
        $defaultRouter = new Definition();
        $this->setDefinition('router.default', $defaultRouter);
        $this->setDefinition('ezpublish.siteaccess', new Definition());
        $this->setDefinition('ezpublish.config.resolver', new Definition());
        $this->setDefinition('ezpublish.siteaccess_router', new Definition());

        $resolverDef = new Definition();
        $serviceId = 'some_service_id';
        if ($declaredPriority !== null) {
            $resolverDef->addTag('router', ['priority' => $declaredPriority]);
        } else {
            $resolverDef->addTag('router');
        }

        $this->setDefinition($serviceId, $resolverDef);
        $this->compile();

        // Assertion for default router
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'router.default',
            'setSiteAccess',
            [new Reference('ezpublish.siteaccess')]
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'router.default',
            'setConfigResolver',
            [new Reference('ezpublish.config.resolver')]
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'router.default',
            'setNonSiteAccessAwareRoutes',
            ['%ezpublish.default_router.non_siteaccess_aware_routes%']
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'router.default',
            'setSiteAccessRouter',
            [new Reference('ezpublish.siteaccess_router')]
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ezpublish.chain_router',
            'add',
            [new Reference('router.default'), 255]
        );

        // Assertion for all routers
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ezpublish.chain_router',
            'add',
            [new Reference($serviceId), $expectedPriority]
        );
    }

    public function addRouterProvider()
    {
        return [
            [null, 0],
            [0, 0],
            [57, 57],
            [-23, -23],
            [-255, -255],
            [-256, -255],
            [-1000, -255],
            [255, 255],
            [256, 255],
            [1000, 255],
        ];
    }
}

class_alias(ChainRoutingPassTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Compiler\ChainRoutingPassTest');
