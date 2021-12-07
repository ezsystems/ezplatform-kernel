<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Bundle\Core\DependencyInjection\Compiler\ChainConfigResolverPass;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\ChainConfigResolver;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers \Ibexa\Bundle\Core\DependencyInjection\Compiler\ChainConfigResolverPass
 */
class ChainConfigResolverPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDefinition(ChainConfigResolver::class, new Definition());
    }

    /**
     * Register the compiler pass under test, just like you would do inside a bundle's load()
     * method:.
     *
     *   $container->addCompilerPass(new MyCompilerPass());
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ChainConfigResolverPass());
    }

    /**
     * @param int|null $declaredPriority
     * @param int $expectedPriority
     *
     * @dataProvider addResolverProvider
     */
    public function testAddResolver($declaredPriority, $expectedPriority)
    {
        $resolverDef = new Definition();
        $serviceId = 'some_service_id';
        if ($declaredPriority !== null) {
            $resolverDef->addTag('ezpublish.config.resolver', ['priority' => $declaredPriority]);
        } else {
            $resolverDef->addTag('ezpublish.config.resolver');
        }

        $this->setDefinition($serviceId, $resolverDef);
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            ChainConfigResolver::class,
            'addResolver',
            [new Reference($serviceId), $expectedPriority]
        );
    }

    public function addResolverProvider()
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

class_alias(ChainConfigResolverPassTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Compiler\ChainConfigResolverPassTest');
