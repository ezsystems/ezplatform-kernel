<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * The ViewPass adds DIC compiler pass related to content view.
 * This includes adding ContentViewProvider implementations.
 *
 * @see \Ibexa\Core\MVC\Symfony\View\Manager
 * @deprecated since 6.0
 *
 * Converts the old tag (ezpublish.xxx_view_provider) to the new one (ezpublish.view_provider with type attribute)
 */
abstract class ViewManagerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds(static::VIEW_PROVIDER_IDENTIFIER) as $id => $attributes) {
            $taggedServiceDefinition = $container->getDefinition($id);
            foreach ($attributes as $attribute) {
                // @todo log deprecated message
                $priority = isset($attribute['priority']) ? (int)$attribute['priority'] : 0;
                $taggedServiceDefinition->clearTag(static::VIEW_PROVIDER_IDENTIFIER);
                $taggedServiceDefinition->addTag(
                    'ezpublish.view_provider',
                    ['type' => static::VIEW_TYPE, 'priority' => $priority]
                );
            }
            $container->setDefinition($id, $taggedServiceDefinition);
        }
    }
}

class_alias(ViewManagerPass::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\ViewManagerPass');
