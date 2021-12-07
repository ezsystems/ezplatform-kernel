<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Base\Container\Compiler\Search\Legacy;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass will register Legacy Storage sort clause handlers.
 */
class SortClauseConverterPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (
            !$container->hasDefinition('ezpublish.search.legacy.gateway.sort_clause_converter.content') &&
            !$container->hasDefinition('ezpublish.search.legacy.gateway.sort_clause_converter.location') &&
            !$container->hasDefinition('ezplatform.trash.search.legacy.gateway.sort_clause_converter')
        ) {
            return;
        }

        if ($container->hasDefinition('ezpublish.search.legacy.gateway.sort_clause_converter.content')) {
            $sortClauseConverterContent = $container->getDefinition('ezpublish.search.legacy.gateway.sort_clause_converter.content');

            $contentHandlers = $container->findTaggedServiceIds('ezpublish.search.legacy.gateway.sort_clause_handler.content');

            $this->addHandlers($sortClauseConverterContent, $contentHandlers);
        }

        if ($container->hasDefinition('ezpublish.search.legacy.gateway.sort_clause_converter.location')) {
            $sortClauseConverterLocation = $container->getDefinition('ezpublish.search.legacy.gateway.sort_clause_converter.location');

            $locationHandlers = $container->findTaggedServiceIds('ezpublish.search.legacy.gateway.sort_clause_handler.location');

            $this->addHandlers($sortClauseConverterLocation, $locationHandlers);
        }

        if ($container->hasDefinition('ezplatform.trash.search.legacy.gateway.sort_clause_converter')) {
            $sortClauseConverterTrash = $container->getDefinition('ezplatform.trash.search.legacy.gateway.sort_clause_converter');

            $trashHandlers = $container->findTaggedServiceIds('ezplatform.trash.search.legacy.gateway.sort_clause_handler');

            $this->addHandlers($sortClauseConverterTrash, $trashHandlers);
        }
    }

    protected function addHandlers(Definition $definition, $handlers)
    {
        foreach ($handlers as $id => $attributes) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}

class_alias(SortClauseConverterPass::class, 'eZ\Publish\Core\Base\Container\Compiler\Search\Legacy\SortClauseConverterPass');
