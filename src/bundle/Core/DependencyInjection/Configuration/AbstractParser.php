<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;

abstract class AbstractParser implements ParserInterface
{
    /**
     * This method is called by the ConfigurationProcessor before looping over available scopes.
     * You may here use $contextualizer->mapConfigArray().
     *
     * @see ConfigurationProcessor::mapConfig()
     * @see \Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface::mapConfigArray()
     *
     * @param array $config Complete parsed semantic configuration
     * @param \Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface $contextualizer
     *
     * @return mixed
     */
    public function preMap(array $config, ContextualizerInterface $contextualizer)
    {
    }

    /**
     * This method is called by the ConfigurationProcessor after looping over available scopes.
     * You may here use $contextualizer->mapConfigArray().
     *
     * @see ConfigurationProcessor::mapConfig()
     * @see \Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface::mapConfigArray()
     *
     * @param array $config Complete parsed semantic configuration
     * @param \Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface $contextualizer
     *
     * @return mixed
     */
    public function postMap(array $config, ContextualizerInterface $contextualizer)
    {
    }
}

class_alias(AbstractParser::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\AbstractParser');
