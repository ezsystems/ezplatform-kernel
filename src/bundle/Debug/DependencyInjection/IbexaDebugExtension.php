<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Debug\DependencyInjection;

use Ibexa\Bundle\Debug\Twig\DebugTemplate;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class IbexaDebugExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        // Base services and services overrides
        $loader->load('services.yml');
    }

    /**
     * Sets the twig base template class to this bundle's in order to collect template infos.
     */
    public function prepend(ContainerBuilder $container)
    {
        if ($container->getParameter('kernel.debug')) {
            $container->prependExtensionConfig(
                'twig',
                ['base_template_class' => DebugTemplate::class]
            );
        }
    }
}

class_alias(IbexaDebugExtension::class, 'eZ\Bundle\EzPublishDebugBundle\DependencyInjection\EzPublishDebugExtension');
