<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\UrlAlias;

use Ibexa\Core\Persistence\Legacy\Content\UrlAlias\SlugConverter;
use Ibexa\Core\Persistence\TransformationProcessor;
use Ibexa\Core\Persistence\TransformationProcessor\PcreCompiler;
use Ibexa\Core\Persistence\TransformationProcessor\PreprocessedBased;
use Ibexa\Core\Persistence\Utf8Converter;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;
use PHPUnit\Framework\TestSuite;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\UrlAlias\SlugConverter
 */
class SlugConverterTest extends TestCase
{
    /**
     * Test for the convert() method.
     */
    public function testConvert()
    {
        $slugConverter = $this->getSlugConverterMock(['cleanupText']);
        $transformationProcessor = $this->getTransformationProcessorMock();

        $text = 'test text  č ';
        $transformedText = 'test text  c ';
        $slug = 'test_text_c';

        $transformationProcessor->expects($this->atLeastOnce())
            ->method('transform')
            ->with($text, ['test_command1'])
            ->will($this->returnValue($transformedText));

        $slugConverter->expects($this->once())
            ->method('cleanupText')
            ->with($this->equalTo($transformedText), $this->equalTo('test_cleanup1'))
            ->will($this->returnValue($slug));

        $this->assertEquals(
            $slug,
            $slugConverter->convert($text)
        );
    }

    /**
     * Test for the convert() method.
     */
    public function testConvertWithDefaultTextFallback()
    {
        $slugConverter = $this->getSlugConverterMock(['cleanupText']);
        $transformationProcessor = $this->getTransformationProcessorMock();

        $defaultText = 'test text  č ';
        $transformedText = 'test text  c ';
        $slug = 'test_text_c';

        $transformationProcessor->expects($this->atLeastOnce())
            ->method('transform')
            ->with($defaultText, ['test_command1'])
            ->will($this->returnValue($transformedText));

        $slugConverter->expects($this->once())
            ->method('cleanupText')
            ->with($this->equalTo($transformedText), $this->equalTo('test_cleanup1'))
            ->will($this->returnValue($slug));

        $this->assertEquals(
            $slug,
            $slugConverter->convert('', $defaultText)
        );
    }

    /**
     * Test for the convert() method.
     */
    public function testConvertWithGivenTransformation()
    {
        $slugConverter = $this->getSlugConverterMock(['cleanupText']);
        $transformationProcessor = $this->getTransformationProcessorMock();

        $text = 'test text  č ';
        $transformedText = 'test text  c ';
        $slug = 'test_text_c';

        $transformationProcessor->expects($this->atLeastOnce())
            ->method('transform')
            ->with($text, ['test_command2'])
            ->will($this->returnValue($transformedText));

        $slugConverter->expects($this->once())
            ->method('cleanupText')
            ->with($this->equalTo($transformedText), $this->equalTo('test_cleanup2'))
            ->will($this->returnValue($slug));

        $this->assertEquals(
            $slug,
            $slugConverter->convert($text, '_1', 'testTransformation2')
        );
    }

    public function providerForTestGetUniqueCounterValue()
    {
        return [
            ['reserved', true, 2],
            ['reserved', false, 1],
            ['not-reserved', true, 1],
            ['not-reserved', false, 1],
        ];
    }

    /**
     * Test for the getUniqueCounterValue() method.
     *
     * @dataProvider providerForTestGetUniqueCounterValue
     */
    public function testGetUniqueCounterValue($text, $isRootLevel, $returnValue)
    {
        $slugConverter = $this->getMockedSlugConverter();

        $this->assertEquals(
            $returnValue,
            $slugConverter->getUniqueCounterValue($text, $isRootLevel)
        );
    }

    public function cleanupTextData()
    {
        return [
            [
                '.Ph\'nglui mglw\'nafh, Cthulhu R\'lyeh wgah\'nagl fhtagn!?...',
                'url_cleanup',
                'Ph-nglui-mglw-nafh-Cthulhu-R-lyeh-wgah-nagl-fhtagn!',
            ],
            [
                '.Ph\'nglui mglw\'nafh, Cthulhu R\'lyeh wgah\'nagl fhtagn!?...',
                'url_cleanup_iri',
                'Ph\'nglui-mglw\'nafh,-Cthulhu-R\'lyeh-wgah\'nagl-fhtagn!',
            ],
            [
                '.Ph\'nglui mglw\'nafh, Cthulhu R\'lyeh wgah\'nagl fhtagn!?...',
                'url_cleanup_compat',
                'ph_nglui_mglw_nafh_cthulhu_r_lyeh_wgah_nagl_fhtagn',
            ],
        ];
    }

    /**
     * Test for the cleanupText() method.
     *
     * @dataProvider cleanupTextData
     */
    public function testCleanupText($text, $method, $expected)
    {
        $testMethod = new \ReflectionMethod(
            SlugConverter::class,
            'cleanupText'
        );
        $testMethod->setAccessible(true);

        $actual = $testMethod->invoke($this->getMockedSlugConverter(), $text, $method);

        $this->assertEquals(
            $expected,
            $actual
        );
    }

    public function convertData()
    {
        return [
            [
                '.Ph\'nglui mglw\'nafh, Cthulhu R\'lyeh wgah\'nagl fhtagn!?...',
                '\'_1\'',
                'urlalias',
                'Ph-nglui-mglw-nafh-Cthulhu-R-lyeh-wgah-nagl-fhtagn!',
            ],
            [
                '.Ph\'nglui mglw\'nafh, Cthulhu R\'lyeh wgah\'nagl fhtagn!?...',
                '\'_1\'',
                'urlalias_iri',
                'Ph\'nglui-mglw\'nafh,-Cthulhu-R\'lyeh-wgah\'nagl-fhtagn!',
            ],
            [
                '.Ph\'nglui mglw\'nafh, Cthulhu R\'lyeh wgah\'nagl fhtagn!?...',
                '\'_1\'',
                'urlalias_compat',
                'ph_nglui_mglw_nafh_cthulhu_r_lyeh_wgah_nagl_fhtagn',
            ],
        ];
    }

    /**
     * Test for the convert() method.
     *
     * @dataProvider convertData
     *
     * @depends testCleanupText
     */
    public function testConvertNoMocking($text, $defaultText, $transformation, $expected)
    {
        $transformationProcessor = new PreprocessedBased(
            new PcreCompiler(
                new Utf8Converter()
            ),
            [
                __DIR__ . '/../../../TransformationProcessor/_fixtures/transformations/ascii.tr.result',
                __DIR__ . '/../../../TransformationProcessor/_fixtures/transformations/basic.tr.result',
                __DIR__ . '/../../../TransformationProcessor/_fixtures/transformations/latin.tr.result',
                __DIR__ . '/../../../TransformationProcessor/_fixtures/transformations/search.tr.result',
            ]
        );
        $slugConverter = new SlugConverter($transformationProcessor);

        $this->assertEquals(
            $expected,
            $slugConverter->convert($text, $defaultText, $transformation)
        );
    }

    /** @var array */
    protected $configuration = [
        'transformation' => 'testTransformation1',
        'transformationGroups' => [
            'testTransformation1' => [
                'commands' => [
                    'test_command1',
                ],
                'cleanupMethod' => 'test_cleanup1',
            ],
            'testTransformation2' => [
                'commands' => [
                    'test_command2',
                ],
                'cleanupMethod' => 'test_cleanup2',
            ],
        ],
        'reservedNames' => [
            'reserved',
        ],
    ];

    /** @var \Ibexa\Core\Persistence\Legacy\Content\UrlAlias\SlugConverter */
    protected $slugConverter;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $slugConverterMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $transformationProcessorMock;

    /**
     * @return \Ibexa\Core\Persistence\Legacy\Content\UrlAlias\SlugConverter
     */
    protected function getMockedSlugConverter()
    {
        if (!isset($this->slugConverter)) {
            $this->slugConverter = new SlugConverter(
                $this->getTransformationProcessorMock(),
                $this->configuration
            );
        }

        return $this->slugConverter;
    }

    /**
     * @param array $methods
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\UrlAlias\SlugConverter|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getSlugConverterMock(array $methods = [])
    {
        if (!isset($this->slugConverterMock)) {
            $this->slugConverterMock = $this->getMockBuilder(SlugConverter::class)
                ->setMethods($methods)
                ->setConstructorArgs(
                    [
                        $this->getTransformationProcessorMock(),
                        $this->configuration,
                    ]
                )
                ->getMock();
        }

        return $this->slugConverterMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getTransformationProcessorMock()
    {
        if (!isset($this->transformationProcessorMock)) {
            $this->transformationProcessorMock = $this->getMockForAbstractClass(
                TransformationProcessor::class,
                [],
                '',
                false,
                true,
                true,
                ['transform']
            );
        }

        return $this->transformationProcessorMock;
    }

    /**
     * Returns the test suite with all tests declared in this class.
     *
     * @return \PHPUnit\Framework\TestSuite
     */
    public static function suite()
    {
        return new TestSuite(__CLASS__);
    }
}

class_alias(SlugConverterTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\UrlAlias\SlugConverterTest');
