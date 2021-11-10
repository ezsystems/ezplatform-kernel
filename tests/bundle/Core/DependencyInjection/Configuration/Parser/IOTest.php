<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ComplexSettings\ComplexSettingParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\IO;
use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Symfony\Component\Yaml\Yaml;

class IOTest extends AbstractParserTestCase
{
    private $minimalConfig;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('ezsettings.default.var_dir', 'var'); // PS: Does not seem to take effect
        $this->container->setParameter('ezsettings.default.storage_dir', 'storage');
        $this->container->setParameter('ezsettings.ezdemo_site.var_dir', 'var/ezdemo_site');
    }

    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([new IO(new ComplexSettingParser())]),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return $this->minimalConfig = Yaml::parse(file_get_contents(__DIR__ . '/../../Fixtures/ezpublish_minimal.yml'));
    }

    public function testHandlersConfig()
    {
        $config = [
            'system' => [
                'ezdemo_site' => [
                    'io' => [
                        'binarydata_handler' => 'cluster',
                        'metadata_handler' => 'cluster',
                    ],
                ],
                self::EMPTY_SA_GROUP => [
                    'io' => [
                        'binarydata_handler' => 'group_cluster',
                        'metadata_handler' => 'group_cluster',
                    ],
                ],
            ],
        ];

        $this->load($config);

        $this->assertConfigResolverParameterValue('io.metadata_handler', 'cluster', 'ezdemo_site');
        $this->assertConfigResolverParameterValue('io.binarydata_handler', 'cluster', 'ezdemo_site');
        $this->assertConfigResolverParameterValue('io.metadata_handler', 'group_cluster', self::EMPTY_SA_GROUP);
        $this->assertConfigResolverParameterValue('io.binarydata_handler', 'group_cluster', self::EMPTY_SA_GROUP);
    }
}

class_alias(IOTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Configuration\Parser\IOTest');
