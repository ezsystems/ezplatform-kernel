<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Base\Container\Compiler\Search;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass will register Search Engine field value mappers.
 */
class AggregateFieldValueMapperPass implements CompilerPassInterface
{
    public const SERVICE_ID = 'ezpublish.search.common.field_value_mapper.aggregate';
    public const TAG = 'ezpublish.search.common.field_value_mapper';

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(self::SERVICE_ID)) {
            return;
        }

        $aggregateFieldValueMapperDefinition = $container->getDefinition(self::SERVICE_ID);
        $taggedServiceIds = $container->findTaggedServiceIds(self::TAG);
        foreach ($taggedServiceIds as $id => $tags) {
            foreach ($tags as $tagAttributes) {
                $aggregateFieldValueMapperDefinition->addMethodCall(
                    'addMapper',
                    [new Reference($id), $tagAttributes['maps'] ?? null]
                );
            }
        }
    }
}
