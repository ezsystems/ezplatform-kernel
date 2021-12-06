<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Compiler;

use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This compiler pass will create aliases for storage engine database handler connections
 * to the storage connection factory.
 */
class StorageConnectionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('ezpublish.storageEngine') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['alias'])) {
                    throw new LogicException(
                        'ezpublish.storageEngine service tag needs an "alias" attribute to ' .
                        'identify the storage engine.'
                    );
                }

                $alias = $attribute['alias'];

                $container->setAlias(
                    "ezpublish.api.storage_engine.{$alias}.connection",
                    'ezpublish.persistence.connection'
                );
            }
        }
    }
}

class_alias(StorageConnectionPass::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\StorageConnectionPass');
