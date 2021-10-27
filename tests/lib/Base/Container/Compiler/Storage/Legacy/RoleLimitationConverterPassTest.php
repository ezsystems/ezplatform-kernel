<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Base\Container\Compiler\Storage\Legacy;

use Ibexa\Core\Base\Container\Compiler\Storage\Legacy\RoleLimitationConverterPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RoleLimitationConverterPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDefinition(
            'ezpublish.persistence.legacy.role.limitation.converter',
            new Definition()
        );
    }

    /**
     * Register the compiler pass under test, just like you would do inside a bundle's load()
     * method:.
     *
     *   $container->addCompilerPass(new MyCompilerPass());
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RoleLimitationConverterPass());
    }

    public function testRegisterRoleLimitationConverter()
    {
        $serviceId = 'service_id';
        $def = new Definition();
        $def->addTag('ezpublish.persistence.legacy.role.limitation.handler');
        $this->setDefinition($serviceId, $def);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ezpublish.persistence.legacy.role.limitation.converter',
            'addHandler',
            [new Reference($serviceId)]
        );
    }
}

class_alias(RoleLimitationConverterPassTest::class, 'eZ\Publish\Core\Base\Tests\Container\Compiler\Storage\Legacy\RoleLimitationConverterPassTest');
