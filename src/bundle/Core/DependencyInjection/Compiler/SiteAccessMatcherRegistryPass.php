<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Bundle\Core\SiteAccess\SiteAccessMatcherRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * The SiteAccessMatcherRegistryPass will register all services tagged as "ezplatform.siteaccess.matcher" to the registry.
 */
final class SiteAccessMatcherRegistryPass implements CompilerPassInterface
{
    public const MATCHER_TAG = 'ezplatform.siteaccess.matcher';

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(SiteAccessMatcherRegistry::class)) {
            return;
        }

        $matcherServiceRegistry = $container->getDefinition(SiteAccessMatcherRegistry::class);

        foreach ($container->findTaggedServiceIds(self::MATCHER_TAG) as $id => $attributes) {
            $matcherServiceRegistry->addMethodCall(
                'setMatcher',
                [
                    $id,
                    new Reference($id),
                ]
            );
        }
    }
}

class_alias(SiteAccessMatcherRegistryPass::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\SiteAccessMatcherRegistryPass');
