<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Matcher;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigResolver;
use Ibexa\Core\MVC\Symfony\Matcher\ClassNameMatcherFactory;
use Ibexa\Core\MVC\Symfony\Matcher\DynamicallyConfiguredMatcherFactoryDecorator;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use PHPUnit\Framework\TestCase;

class DynamicallyConfiguredMatcherFactoryDecoratorTest extends TestCase
{
    /** @var \Ibexa\Core\MVC\Symfony\Matcher\ConfigurableMatcherFactoryInterface */
    private $innerMatcherFactory;

    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function setUp(): void
    {
        $innerMatcherFactory = $this->createMock(ClassNameMatcherFactory::class);
        $configResolver = $this->createMock(ConfigResolver::class);

        $this->innerMatcherFactory = $innerMatcherFactory;
        $this->configResolver = $configResolver;
    }

    /**
     * @dataProvider matchConfigProvider
     */
    public function testMatch($parameterName, $namespace, $scope, $viewsConfiguration, $matchedConfig): void
    {
        $view = $this->createMock(ContentView::class);
        $this->configResolver->expects($this->atLeastOnce())->method('getParameter')->with(
            $parameterName,
            $namespace,
            $scope
        )->willReturn($viewsConfiguration);
        $this->innerMatcherFactory->expects($this->once())->method('match')->with($view)->willReturn($matchedConfig);

        $matcherFactory = new DynamicallyConfiguredMatcherFactoryDecorator(
            $this->innerMatcherFactory,
            $this->configResolver,
            $parameterName,
            $namespace,
            $scope
        );

        $this->assertEquals($matchedConfig, $matcherFactory->match($view));
    }

    public function matchConfigProvider(): array
    {
        return [
            [
                'location_view',
                null,
                null,
                [
                    'full' => [
                        'test' => [
                            'template' => 'foo.html.twig',
                            'match' => [
                                \stdClass::class => true,
                            ],
                        ],
                    ],
                ],
                [
                    'template' => 'foo.html.twig',
                    'match' => [
                        \stdClass::class => true,
                    ],
                ],
            ],
        ];
    }
}

class_alias(DynamicallyConfiguredMatcherFactoryDecoratorTest::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\Tests\DynamicallyConfiguredMatcherFactoryDecoratorTest');
