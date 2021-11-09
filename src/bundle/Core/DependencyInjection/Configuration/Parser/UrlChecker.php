<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class UrlChecker extends AbstractParser
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('url_checker')
                ->children()
                    ->arrayNode('handlers')
                        ->prototype('variable')
                        ->end()
                        ->useAttributeAsKey('name')
                    ->end()
                ->end()
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (isset($scopeSettings['url_checker']) && !empty($scopeSettings['url_checker']['handlers'])) {
            foreach ($scopeSettings['url_checker']['handlers'] as $name => $options) {
                $contextualizer->setContextualParameter('url_handler.' . $name . '.options', $currentScope, $options);
            }
        }
    }
}

class_alias(UrlChecker::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\UrlChecker');
