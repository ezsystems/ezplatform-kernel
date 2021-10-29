<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\DependencyInjection\Compiler;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ConsoleCommandPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('console.command') as $id => $attributes) {
            $definition = $container->getDefinition($id);

            $definition->addMethodCall('addOption', [
                'siteaccess',
                null,
                InputOption::VALUE_OPTIONAL,
                'SiteAccess to use for operations. If not provided, default siteaccess will be used',
            ]);
        }
    }
}

class_alias(ConsoleCommandPass::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\ConsoleCommandPass');
