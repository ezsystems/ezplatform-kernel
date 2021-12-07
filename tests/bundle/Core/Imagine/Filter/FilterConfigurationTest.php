<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Imagine\Filter;

use Ibexa\Bundle\Core\Imagine\Filter\FilterConfiguration;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidVariationException;
use Ibexa\Core\MVC\ConfigResolverInterface;
use PHPUnit\Framework\TestCase;

class FilterConfigurationTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    /** @var \Ibexa\Bundle\Core\Imagine\Filter\FilterConfiguration */
    private $filterConfiguration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->filterConfiguration = new FilterConfiguration();
        $this->filterConfiguration->setConfigResolver($this->configResolver);
    }

    public function testGetOnlyImagineFilters()
    {
        $fooConfig = ['fooconfig'];
        $barConfig = ['barconfig'];
        $this->filterConfiguration->set('foo', $fooConfig);
        $this->filterConfiguration->set('bar', $barConfig);

        $this->configResolver
            ->expects($this->exactly(2))
            ->method('getParameter')
            ->with('image_variations')
            ->will($this->returnValue([]));

        $this->assertSame($fooConfig, $this->filterConfiguration->get('foo'));
        $this->assertSame($barConfig, $this->filterConfiguration->get('bar'));
    }

    public function testGetNoEzVariationInvalidImagineFilter()
    {
        $this->expectException(\RuntimeException::class);

        $fooConfig = ['fooconfig'];
        $barConfig = ['barconfig'];
        $this->filterConfiguration->set('foo', $fooConfig);
        $this->filterConfiguration->set('bar', $barConfig);

        $this->configResolver
            ->expects($this->once())
            ->method('getParameter')
            ->with('image_variations')
            ->will($this->returnValue([]));

        $this->filterConfiguration->get('foobar');
    }

    public function testGetWithEzVariationInvalidFilters()
    {
        $this->expectException(InvalidVariationException::class);

        $fooConfig = ['fooconfig'];
        $barConfig = ['barconfig'];
        $this->filterConfiguration->set('foo', $fooConfig);
        $this->filterConfiguration->set('bar', $barConfig);

        $variations = [
            'some_variation' => [],
        ];
        $this->configResolver
            ->expects($this->once())
            ->method('getParameter')
            ->with('image_variations')
            ->will($this->returnValue($variations));

        $this->filterConfiguration->get('some_variation');
    }

    public function testGetEzVariationNoReference()
    {
        $fooConfig = ['fooconfig'];
        $barConfig = ['barconfig'];
        $this->filterConfiguration->set('foo', $fooConfig);
        $this->filterConfiguration->set('bar', $barConfig);

        $filters = ['some_filter' => []];
        $variations = [
            'some_variation' => ['filters' => $filters],
        ];
        $this->configResolver
            ->expects($this->once())
            ->method('getParameter')
            ->with('image_variations')
            ->will($this->returnValue($variations));

        $this->assertSame(
            [
                'cache' => 'ibexa',
                'data_loader' => 'ibexa',
                'reference' => null,
                'filters' => $filters,
                'post_processors' => [],
            ],
            $this->filterConfiguration->get('some_variation')
        );
    }

    public function testGetEzVariationWithReference()
    {
        $fooConfig = ['fooconfig'];
        $barConfig = ['barconfig'];
        $this->filterConfiguration->set('foo', $fooConfig);
        $this->filterConfiguration->set('bar', $barConfig);

        $filters = ['some_filter' => []];
        $reference = 'another_variation';
        $variations = [
            'some_variation' => ['filters' => $filters, 'reference' => $reference],
        ];
        $this->configResolver
            ->expects($this->once())
            ->method('getParameter')
            ->with('image_variations')
            ->will($this->returnValue($variations));

        $this->assertSame(
            [
                'cache' => 'ibexa',
                'data_loader' => 'ibexa',
                'reference' => $reference,
                'filters' => $filters,
                'post_processors' => [],
            ],
            $this->filterConfiguration->get('some_variation')
        );
    }

    public function testGetEzVariationImagineFilters()
    {
        $filters = ['some_filter' => []];
        $imagineConfig = ['filters' => $filters];
        $this->filterConfiguration->set('some_variation', $imagineConfig);

        $reference = 'another_variation';
        $variations = [
            'some_variation' => ['reference' => $reference],
        ];
        $this->configResolver
            ->expects($this->once())
            ->method('getParameter')
            ->with('image_variations')
            ->will($this->returnValue($variations));

        $this->assertSame(
            [
                'cache' => 'ibexa',
                'data_loader' => 'ibexa',
                'reference' => $reference,
                'filters' => $filters,
                'post_processors' => [],
            ],
            $this->filterConfiguration->get('some_variation')
        );
    }

    public function testGetEzVariationImagineOptions()
    {
        $imagineConfig = [
            'foo_option' => 'foo',
            'bar_option' => 'bar',
        ];
        $this->filterConfiguration->set('some_variation', $imagineConfig);

        $filters = ['some_filter' => []];
        $reference = 'another_variation';
        $variations = [
            'some_variation' => ['reference' => $reference, 'filters' => $filters],
        ];
        $this->configResolver
            ->expects($this->once())
            ->method('getParameter')
            ->with('image_variations')
            ->will($this->returnValue($variations));

        $this->assertSame(
            [
                'cache' => 'ibexa',
                'data_loader' => 'ibexa',
                'reference' => $reference,
                'filters' => $filters,
                'post_processors' => [],
                'foo_option' => 'foo',
                'bar_option' => 'bar',
            ],
            $this->filterConfiguration->get('some_variation')
        );
    }

    public function testAll()
    {
        $fooConfig = ['fooconfig'];
        $barConfig = ['barconfig'];
        $this->filterConfiguration->set('foo', $fooConfig);
        $this->filterConfiguration->set('bar', $barConfig);
        $this->filterConfiguration->set('some_variation', []);

        $filters = ['some_filter' => []];
        $reference = 'another_variation';
        $eZVariationConfig = ['filters' => $filters, 'reference' => $reference];
        $variations = ['some_variation' => $eZVariationConfig];
        $this->configResolver
            ->expects($this->once())
            ->method('getParameter')
            ->with('image_variations')
            ->will($this->returnValue($variations));

        $this->assertEquals(
            [
                'foo' => $fooConfig,
                'bar' => $barConfig,
                'some_variation' => $eZVariationConfig,
            ],
            $this->filterConfiguration->all()
        );
    }
}

class_alias(FilterConfigurationTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Imagine\Filter\FilterConfigurationTest');
