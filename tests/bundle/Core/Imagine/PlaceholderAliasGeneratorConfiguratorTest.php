<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Imagine;

use Ibexa\Bundle\Core\Imagine\PlaceholderAliasGenerator;
use Ibexa\Bundle\Core\Imagine\PlaceholderAliasGeneratorConfigurator;
use Ibexa\Bundle\Core\Imagine\PlaceholderProvider;
use Ibexa\Bundle\Core\Imagine\PlaceholderProviderRegistry;
use Ibexa\Core\MVC\ConfigResolverInterface;
use PHPUnit\Framework\TestCase;

class PlaceholderAliasGeneratorConfiguratorTest extends TestCase
{
    public const BINARY_HANDLER_NAME = 'default';
    public const PROVIDER_TYPE = 'generic';
    public const PROVIDER_OPTIONS = [
        'a' => 'A',
        'b' => 'B',
        'c' => 'C',
    ];

    public function testConfigure()
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver
            ->expects($this->once())
            ->method('getParameter')
            ->with('io.binarydata_handler')
            ->willReturn(self::BINARY_HANDLER_NAME);

        $provider = $this->createMock(PlaceholderProvider::class);

        $providerRegistry = $this->createMock(PlaceholderProviderRegistry::class);
        $providerRegistry
            ->expects($this->once())
            ->method('getProvider')
            ->with(self::PROVIDER_TYPE)
            ->willReturn($provider);

        $providerConfig = [
            self::BINARY_HANDLER_NAME => [
                'provider' => self::PROVIDER_TYPE,
                'options' => self::PROVIDER_OPTIONS,
            ],
        ];

        $generator = $this->createMock(PlaceholderAliasGenerator::class);
        $generator
            ->expects($this->once())
            ->method('setPlaceholderProvider')
            ->with($provider, self::PROVIDER_OPTIONS);

        $configurator = new PlaceholderAliasGeneratorConfigurator(
            $configResolver,
            $providerRegistry,
            $providerConfig
        );
        $configurator->configure($generator);
    }
}

class_alias(PlaceholderAliasGeneratorConfiguratorTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Imagine\PlaceholderAliasGeneratorConfiguratorTest');
