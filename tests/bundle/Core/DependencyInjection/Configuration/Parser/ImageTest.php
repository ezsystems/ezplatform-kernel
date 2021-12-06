<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\Image;
use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Symfony\Component\Yaml\Yaml;

class ImageTest extends AbstractParserTestCase
{
    private $config;

    protected function setUp(): void
    {
        parent::setUp();

        if (!isset($_ENV['imagemagickConvertPath']) || !is_executable($_ENV['imagemagickConvertPath'])) {
            $this->markTestSkipped('Missing or mis-configured Imagemagick convert path.');
        }
    }

    protected function getMinimalConfiguration(): array
    {
        $this->config = Yaml::parse(file_get_contents(__DIR__ . '/../../Fixtures/ezpublish_image.yml'));
        $this->config += [
            'imagemagick' => [
                'enabled' => true,
                'path' => $_ENV['imagemagickConvertPath'],
            ],
        ];

        return $this->config;
    }

    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([new Image()]),
        ];
    }

    public function testVariations()
    {
        $this->load();

        $expectedParsedVariations = [];
        foreach ($this->config['system'] as $sa => $saConfig) {
            $expectedParsedVariations[$sa] = [];
            foreach ($saConfig['image_variations'] as $variationName => $imageVariationConfig) {
                $imageVariationConfig['post_processors'] = [];
                foreach ($imageVariationConfig['filters'] as $i => $filter) {
                    $imageVariationConfig['filters'][$filter['name']] = $filter['params'];
                    unset($imageVariationConfig['filters'][$i]);
                }
                $expectedParsedVariations[$sa][$variationName] = $imageVariationConfig;
            }
        }

        $expected = $expectedParsedVariations['ezdemo_group'] + $this->container->getParameter('ezsettings.default.image_variations');
        $this->assertConfigResolverParameterValue('image_variations', $expected, 'ezdemo_site', false);
        $this->assertConfigResolverParameterValue('image_variations', $expected, 'ezdemo_site_admin', false);
        $this->assertConfigResolverParameterValue(
            'image_variations',
            $expected + $expectedParsedVariations['fre'],
            'fre',
            false
        );
    }

    public function testPrePostParameters()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->load(
            [
                'system' => [
                    'ezdemo_site' => [
                        'imagemagick' => [
                            'pre_parameters' => '-foo -bar',
                            'post_parameters' => '-baz',
                        ],
                    ],
                ],
            ]
        );
    }
}

class_alias(ImageTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Configuration\Parser\ImageTest');
