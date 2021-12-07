<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Compiler;

use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass will register eZ Publish storage engines.
 */
class RegisterStorageEnginePass implements CompilerPassInterface
{
    /**
     * Performs compiler passes for persistence factories.
     *
     * Does:
     * - Registers all storage engines to ezpublish.api.storage_engine.factory
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \LogicException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ezpublish.api.storage_engine.factory')) {
            return;
        }

        $storageEngineFactoryDef = $container->getDefinition('ezpublish.api.storage_engine.factory');
        foreach ($container->findTaggedServiceIds('ezpublish.storageEngine') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['alias'])) {
                    throw new LogicException('ezpublish.storageEngine service tag needs an "alias" attribute to identify the storage engine.');
                }

                // Register the storage engine on the main storage engine factory
                $storageEngineFactoryDef->addMethodCall(
                    'registerStorageEngine',
                    [
                        new Reference($id),
                        $attribute['alias'],
                    ]
                );
            }
        }
    }
}

class_alias(RegisterStorageEnginePass::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\RegisterStorageEnginePass');
