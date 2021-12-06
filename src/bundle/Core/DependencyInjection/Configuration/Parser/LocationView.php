<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigResolver;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;

class LocationView extends View
{
    public const NODE_KEY = 'location_view';
    public const INFO = 'Template selection settings when displaying a location. Deprecated from 5.4.5/2015.09, use content_view instead.';

    public function preMap(array $config, ContextualizerInterface $contextualizer)
    {
        $scopes = array_merge(
            [ConfigResolver::SCOPE_GLOBAL],
            $config['siteaccess']['list'],
            array_keys($config['siteaccess']['groups']),
            [ConfigResolver::SCOPE_DEFAULT]
        );

        foreach ($scopes as $scope) {
            if (!isset($config[$contextualizer->getSiteAccessNodeName()][$scope][static::NODE_KEY])) {
                continue;
            }

            $locationViewConfig = &$config[$contextualizer->getSiteAccessNodeName()][$scope][static::NODE_KEY];
            $contentViewConfig = &$config[$contextualizer->getSiteAccessNodeName()][$scope][ContentView::NODE_KEY];

            // view rules without a custom controller are moved from $locationViewConfig to $contentViewConfig
            foreach ($locationViewConfig as $viewIdentifier => $viewRules) {
                foreach ($viewRules as $viewRuleIdentifier => $viewRule) {
                    if (!isset($viewRule['controller'])) {
                        $contentViewConfig[$viewIdentifier][$viewRuleIdentifier] =
                            $locationViewConfig[$viewIdentifier][$viewRuleIdentifier];
                        unset($locationViewConfig[$viewIdentifier][$viewRuleIdentifier]);
                    }
                }
                if (count($locationViewConfig[$viewIdentifier]) === 0) {
                    unset($locationViewConfig[$viewIdentifier]);
                }
            }
        }

        parent::preMap($config, $contextualizer);
        $contextualizer->mapConfigArray(ContentView::NODE_KEY, $config, ContextualizerInterface::MERGE_FROM_SECOND_LEVEL);
    }
}

class_alias(LocationView::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\LocationView');
