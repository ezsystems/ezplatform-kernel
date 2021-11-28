<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\TransformationProcessor;

use Ibexa\Core\Persistence;
use Ibexa\Core\Persistence\TransformationProcessor\DefinitionBased;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * Test case for LocationHandlerTest.
 */
class TransformationProcessorDefinitionBasedTest extends TestCase
{
    public function getProcessor()
    {
        return new DefinitionBased(
            new Persistence\TransformationProcessor\DefinitionBased\Parser(),
            new Persistence\TransformationProcessor\PcreCompiler(new Persistence\Utf8Converter()),
            glob(__DIR__ . '/_fixtures/transformations/*.tr')
        );
    }

    public function testSimpleNormalizationLowercase()
    {
        $processor = $this->getProcessor();

        $this->assertSame(
            'hello world!',
            $processor->transform('Hello World!', ['ascii_lowercase'])
        );
    }

    public function testSimpleNormalizationUppercase()
    {
        $processor = $this->getProcessor();

        $this->assertSame(
            'HELLO WORLD!',
            $processor->transform('Hello World!', ['ascii_uppercase'])
        );
    }

    public function testApplyAllLowercaseNormalizations()
    {
        $processor = $this->getProcessor();

        $this->assertSame(
            'hello world!',
            $processor->transformByGroup('Hello World!', 'lowercase')
        );
    }

    /**
     * The main point of this test is, that it shows that all normalizations
     * available can be compiled without errors. The actual expectation is not
     * important.
     */
    public function testAllNormalizations()
    {
        $processor = $this->getProcessor();

        $this->assertSame(
            'HELLO WORLD.',
            $processor->transform('Hello World!')
        );
    }
}

class_alias(TransformationProcessorDefinitionBasedTest::class, 'eZ\Publish\Core\Persistence\Tests\TransformationProcessor\TransformationProcessorDefinitionBasedTest');
