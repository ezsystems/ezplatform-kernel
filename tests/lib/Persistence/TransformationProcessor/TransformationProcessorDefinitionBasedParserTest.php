<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\TransformationProcessor;

use Ibexa\Core\Persistence;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * Test case for LocationHandlerTest.
 */
class TransformationProcessorDefinitionBasedParserTest extends TestCase
{
    public static function getTestFiles()
    {
        return array_map(
            static function ($file) {
                return [realpath($file)];
            },
            glob(__DIR__ . '/_fixtures/transformations/*.tr')
        );
    }

    /**
     * @dataProvider getTestFiles
     */
    public function testParse($file)
    {
        $parser = new Persistence\TransformationProcessor\DefinitionBased\Parser();

        $fixture = include $file . '.result';
        $this->assertEquals(
            $fixture,
            $parser->parse($file)
        );
    }
}

class_alias(TransformationProcessorDefinitionBasedParserTest::class, 'eZ\Publish\Core\Persistence\Tests\TransformationProcessor\TransformationProcessorDefinitionBasedParserTest');
