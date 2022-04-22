<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Base\Container\Compiler\Search\Legacy;

use Ibexa\Core\Persistence\Legacy\Content\URLWildcard\Query\CriteriaConverter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass will register Legacy Search Engine criterion handlers.
 */
class CriteriaConverterPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (
            !$container->hasDefinition('ezpublish.search.legacy.gateway.criteria_converter.content') &&
            !$container->hasDefinition('ezpublish.search.legacy.gateway.criteria_converter.location') &&
            !$container->hasDefinition('ezplatform.trash.search.legacy.gateway.criteria_converter') &&
            !$container->hasDefinition('ezpublish.spi.persistence.legacy.url.criterion_converter')
        ) {
            return;
        }

        if ($container->hasDefinition('ezpublish.search.legacy.gateway.criteria_converter.content')) {
            $criteriaConverterContent = $container->getDefinition('ezpublish.search.legacy.gateway.criteria_converter.content');

            $contentHandlers = $container->findTaggedServiceIds('ezpublish.search.legacy.gateway.criterion_handler.content');

            $this->addHandlers($criteriaConverterContent, $contentHandlers);
        }

        if ($container->hasDefinition('ezpublish.search.legacy.gateway.criteria_converter.location')) {
            $criteriaConverterLocation = $container->getDefinition('ezpublish.search.legacy.gateway.criteria_converter.location');

            $locationHandlers = $container->findTaggedServiceIds('ezpublish.search.legacy.gateway.criterion_handler.location');

            $this->addHandlers($criteriaConverterLocation, $locationHandlers);
        }

        if ($container->hasDefinition('ezplatform.trash.search.legacy.gateway.criteria_converter')) {
            $trashCriteriaConverter = $container->getDefinition('ezplatform.trash.search.legacy.gateway.criteria_converter');
            $trashCriteriaHandlers = $container->findTaggedServiceIds('ezplatform.trash.search.legacy.gateway.criterion_handler');

            $this->addHandlers($trashCriteriaConverter, $trashCriteriaHandlers);
        }

        if ($container->hasDefinition('ezpublish.spi.persistence.legacy.url.criterion_converter')) {
            $urlCriteriaConverter = $container->getDefinition('ezpublish.spi.persistence.legacy.url.criterion_converter');
            $urlCriteriaHandlers = $container->findTaggedServiceIds('ezpublish.persistence.legacy.url.criterion_handler');

            $this->addHandlers($urlCriteriaConverter, $urlCriteriaHandlers);
        }

        if ($container->hasDefinition(CriteriaConverter::class)) {
            $urlWildcardCriteriaConverter = $container->getDefinition(CriteriaConverter::class);
            $urlWildcardCriteriaHandlers = $container->findTaggedServiceIds('ibexa.storage.legacy.url_wildcard.criterion.handler');
            $this->addHandlers($urlWildcardCriteriaConverter, $urlWildcardCriteriaHandlers);
        }
    }

    protected function addHandlers(Definition $definition, $handlers)
    {
        foreach ($handlers as $id => $attributes) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
