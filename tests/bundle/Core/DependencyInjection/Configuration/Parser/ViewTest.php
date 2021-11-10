<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\ContentView;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\LocationView;
use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Symfony\Component\Yaml\Yaml;

class ViewTest extends AbstractParserTestCase
{
    private $config;

    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([new LocationView(), new ContentView()]),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return $this->config = Yaml::parse(file_get_contents(__DIR__ . '/../../Fixtures/ezpublish_view.yml'));
    }

    public function testLocationView()
    {
        $this->load();
        $expectedLocationView = $this->config['system']['ezdemo_frontend_group']['location_view'];

        // Items that don't use a custom controller got converted to content view (location view depreciation)
        unset($expectedLocationView['full']['frontpage']);
        unset($expectedLocationView['line']['article']);

        foreach ($expectedLocationView as &$rulesets) {
            foreach ($rulesets as &$config) {
                if (!isset($config['params'])) {
                    $config['params'] = [];
                }
            }
        }

        $this->assertConfigResolverParameterValue('location_view', $expectedLocationView, 'ezdemo_site', false);
        $this->assertConfigResolverParameterValue('location_view', $expectedLocationView, 'fre', false);
        $this->assertConfigResolverParameterValue('location_view', [], 'ezdemo_site_admin', false);
    }

    public function testContentView()
    {
        $this->load();
        $expectedContentView = $this->config['system']['ezdemo_frontend_group']['content_view'];
        foreach ($expectedContentView as &$rulesets) {
            foreach ($rulesets as &$config) {
                if (!isset($config['params'])) {
                    $config['params'] = [];
                }
            }
        }

        $this->assertConfigResolverParameterValue('content_view', $expectedContentView, 'ezdemo_site', false);
        $this->assertConfigResolverParameterValue('content_view', $expectedContentView, 'fre', false);
        $this->assertConfigResolverParameterValue('content_view', [], 'ezdemo_site_admin', false);
    }
}

class_alias(ViewTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Configuration\Parser\ViewTest');
