<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\DependencyInjection\Compiler;

use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class NotificationRendererPass implements CompilerPassInterface
{
    public const TAG_NAME = 'ezpublish.notification.renderer';
    public const REGISTRY_DEFINITION_ID = 'notification.renderer.registry';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::REGISTRY_DEFINITION_ID)) {
            return;
        }

        $registry = $container->findDefinition(self::REGISTRY_DEFINITION_ID);

        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['alias'])) {
                    throw new LogicException(sprintf(
                        'Tag %s needs an "alias" attribute to identify the notification type.',
                        self::TAG_NAME
                    ));
                }

                $registry->addMethodCall('addRenderer', [$attribute['alias'], new Reference($id)]);
            }
        }
    }
}

class_alias(NotificationRendererPass::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\NotificationRendererPass');
