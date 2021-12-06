<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration;

use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class ContainerConfigBuilder implements ConfigBuilderInterface
{
    /** @var \Symfony\Component\DependencyInjection\ContainerBuilder */
    protected $containerBuilder;

    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
    }
}

class_alias(ContainerConfigBuilder::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ContainerConfigBuilder');
