<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Core\Persistence\Cache\CacheIndicesValidator;
use Ibexa\Core\Persistence\Cache\CacheIndicesValidatorInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class CacheIndicesValidatorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->getParameter('kernel.debug') === false) {
            return;
        }

        $definition = new Definition(CacheIndicesValidator::class);
        $definition->addTag('monolog.logger', ['channel' => 'ibexa.core']);
        $definition->setMethodCalls([
            ['setLogger', [new Reference('logger')]],
        ]);

        $container->setDefinition(CacheIndicesValidator::class, $definition);

        $interfaceDefinition = new Definition(CacheIndicesValidatorInterface::class);
        $container->setDefinition(CacheIndicesValidatorInterface::class, $interfaceDefinition);
        $container->setAlias(
            CacheIndicesValidatorInterface::class,
            new Alias(CacheIndicesValidator::class)
        );

        $abstractInMemoryHandlerDefinition = $container
            ->getDefinition('ezpublish.spi.persistence.cache.abstractInMemoryHandler');
        $abstractInMemoryHandlerDefinition->setArgument(
            '$cacheIndicesValidator',
            new Reference(CacheIndicesValidatorInterface::class)
        );
        $abstractInMemoryPersistenceHandlerDefinition = $container
            ->getDefinition('ezpublish.spi.persistence.cache.abstractInMemoryPersistenceHandler');
        $abstractInMemoryPersistenceHandlerDefinition->setArgument(
            '$cacheIndicesValidator',
            new Reference(CacheIndicesValidatorInterface::class)
        );
    }
}
