<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\Repository;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\RepositoryConfigParserInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

final class FieldGroups implements RepositoryConfigParserInterface
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('fields_groups')
                ->info('Definitions of fields groups.')
                ->children()
                    ->arrayNode('list')->prototype('scalar')->end()->end()
                    ->scalarNode('default')->defaultValue('%ezsettings.default.content.field_groups.default%')->end()
                ->end()
            ->end();
    }
}
