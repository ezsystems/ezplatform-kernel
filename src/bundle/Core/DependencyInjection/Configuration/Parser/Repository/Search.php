<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\Repository;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\RepositoryConfigParserInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

final class Search implements RepositoryConfigParserInterface
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('search')
                ->children()
                    ->scalarNode('engine')
                        ->defaultValue('%ezpublish.api.search_engine.default%')
                        ->info('The search engine to use')
                    ->end()
                    ->scalarNode('connection')
                        ->defaultNull()
                        ->info('The connection name, if applicable (e.g. Doctrine connection name). If not set, the default connection will be used.')
                    ->end()
                    ->arrayNode('config')
                        ->info('Arbitrary configuration options, supported by your search engine')
                        ->useAttributeAsKey('key')
                        ->prototype('variable')->end()
                    ->end()
                ->end()
            ->end();
    }
}
