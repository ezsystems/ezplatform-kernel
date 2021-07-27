<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\Repository;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\RepositoryConfigParserInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

final class Options implements RepositoryConfigParserInterface
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('options')
                ->info('Options for repository.')
                ->children()
                    ->scalarNode('default_version_archive_limit')
                        ->defaultValue(5)
                        ->info('Default version archive limit (0-50), only enforced on publish, not on un-publish.')
                    ->end()
                    ->booleanNode('remove_archived_versions_on_publish')
                        ->defaultTrue()
                        ->info('Enables automatic removal of archived versions when publishing, at the cost of performance. "ezplatform:content:cleanup-versions" command should be used to perform this task instead if this option is set to false.')
                    ->end()
                ->end()
            ->end();
    }
}
