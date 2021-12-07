<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Bundle\Core\DependencyInjection\Compiler\LazyDoctrineRepositoriesPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LazyDoctrineRepositoriesPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new LazyDoctrineRepositoriesPass());
    }

    public function testNonLazyServices(): void
    {
        $myServiceWithEntityManagerFactory = new Definition();
        $myServiceWithEntityManagerFactory->setFactory(
            [new Reference('ibexa.doctrine.orm.entity_manager'), 'getEntityManager']
        );

        $myLazyServiceWithEntityManagerFactory = new Definition();
        $myLazyServiceWithEntityManagerFactory->setLazy(true);
        $myLazyServiceWithEntityManagerFactory->setFactory(
            [new Reference('ibexa.doctrine.orm.entity_manager'), 'getEntityManager']
        );

        $myServiceWithFactory = new Definition();
        $myServiceWithFactory->setFactory([new Reference('my_factory'), 'getService']);
        $myServiceWithFactory->setLazy(true);

        $myOtherServiceWithFactory = new Definition();
        $myOtherServiceWithFactory->setFactory([new Definition('\My\Class'), 'getService']);
        $myOtherServiceWithFactory->setLazy(true);

        $this->setDefinition('my_service', $myServiceWithFactory);
        $this->setDefinition('my_other_service', $myOtherServiceWithFactory);
        $this->setDefinition('my_entity_manager', $myServiceWithEntityManagerFactory);
        $this->setDefinition('my_lazy_entity_manager', $myLazyServiceWithEntityManagerFactory);

        $this->expectException(RuntimeException::class);

        $this->compile();
    }
}

class_alias(LazyDoctrineRepositoriesPassTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Compiler\LazyDoctrineRepositoriesPassTest');
