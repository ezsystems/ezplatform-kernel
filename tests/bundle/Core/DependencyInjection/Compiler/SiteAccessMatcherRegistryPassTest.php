<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Bundle\Core\DependencyInjection\Compiler\SiteAccessMatcherRegistryPass;
use Ibexa\Bundle\Core\SiteAccess\SiteAccessMatcherRegistry;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class SiteAccessMatcherRegistryPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setDefinition(SiteAccessMatcherRegistry::class, new Definition());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new SiteAccessMatcherRegistryPass());
    }

    public function testSetMatcher(): void
    {
        $def = new Definition();
        $def->addTag(SiteAccessMatcherRegistryPass::MATCHER_TAG);
        $serviceId = 'service_id';
        $this->setDefinition($serviceId, $def);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            SiteAccessMatcherRegistry::class,
            'setMatcher',
            [
                $serviceId,
                new Reference($serviceId),
            ]
        );
    }
}

class_alias(SiteAccessMatcherRegistryPassTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Compiler\SiteAccessMatcherRegistryPassTest');
