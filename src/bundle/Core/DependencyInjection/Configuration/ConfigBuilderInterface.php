<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration;

use Symfony\Component\Config\Resource\ResourceInterface;

/**
 * Interface for config builders.
 * Config builders can be used to add/extend configuration.
 */
interface ConfigBuilderInterface
{
    /**
     * Adds config to the builder.
     *
     * @param array $config
     */
    public function addConfig(array $config);

    /**
     * Adds given resource, which would typically be added to container resources.
     *
     * @param \Symfony\Component\Config\Resource\ResourceInterface $resource
     */
    public function addResource(ResourceInterface $resource);
}

class_alias(ConfigBuilderInterface::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface');
