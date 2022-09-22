<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Security\PolicyProvider;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\PoliciesConfigBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PoliciesConfigBuilderTest extends TestCase
{
    /**
     * @dataProvider policiesConfigProvider
     */
    public function testAddConfig(array $configOne, array $configTwo, array $expectedConfig): void
    {
        $containerBuilder = new ContainerBuilder();
        $configBuilder = new PoliciesConfigBuilder($containerBuilder);

        $configBuilder->addConfig($configOne);
        $configBuilder->addConfig($configTwo);

        self::assertSame($expectedConfig, $containerBuilder->getParameter('ezpublish.api.role.policy_map'));
    }

    public function policiesConfigProvider(): array
    {
        return [
            'add' => [
                ['foo' => ['bar' => null]],
                ['some' => ['thing' => ['limitation']]],
                [
                    'foo' => ['bar' => []],
                    'some' => ['thing' => ['limitation' => true]],
                ],
            ],
            'append' => [
                ['foo' => ['bar' => ['limitation']]],
                ['foo' => ['bar' => ['new_limitation']]],
                [
                    'foo' => ['bar' => ['limitation' => true, 'new_limitation' => true]],
                ],
            ],
            'append_to_empty' => [
                ['foo' => ['bar' => null]],
                ['foo' => ['bar' => ['new_limitation']]],
                [
                    'foo' => ['bar' => ['new_limitation' => true]],
                ],
            ],
        ];
    }

    public function testAddResource()
    {
        $containerBuilder = new ContainerBuilder();
        $configBuilder = new PoliciesConfigBuilder($containerBuilder);
        $resource1 = new FileResource(__FILE__);
        $resource2 = new DirectoryResource(__DIR__);
        $configBuilder->addResource($resource1);
        $configBuilder->addResource($resource2);

        self::assertSame([$resource1, $resource2], $containerBuilder->getResources());
    }
}
