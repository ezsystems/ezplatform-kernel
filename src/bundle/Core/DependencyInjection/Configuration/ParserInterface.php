<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\HookableConfigurationMapperInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

interface ParserInterface extends HookableConfigurationMapperInterface
{
    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder);
}

class_alias(ParserInterface::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ParserInterface');
