<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Bundle\Core\Routing\DefaultRouter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Routing related compiler pass.
 *
 * Manipulates Symfony default router services to adapt them to eZ routing needs,
 * specifically to implement the RequestMatcherInterface.
 */
class RouterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('router.default')) {
            return;
        }

        $container
            ->findDefinition('router.default')
            ->setClass(DefaultRouter::class);
    }
}

class_alias(RouterPass::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\RouterPass');
