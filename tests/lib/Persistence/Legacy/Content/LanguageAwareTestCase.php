<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content;

use Ibexa\Core\Persistence;
use Ibexa\Core\Persistence\Legacy\Content\Language\MaskGenerator as LanguageMaskGenerator;
use Ibexa\Core\Search\Common\FieldNameGenerator;
use Ibexa\Core\Search\Common\FieldRegistry;
use Ibexa\Core\Search\Legacy\Content\Mapper\FullTextMapper;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * Test case for Language aware classes.
 */
abstract class LanguageAwareTestCase extends TestCase
{
    protected const ENG_GB = 'eng-GB';

    /**
     * Language handler.
     *
     * @var \Ibexa\Contracts\Core\Persistence\Content\Language\Handler
     */
    protected $languageHandler;

    /**
     * Language mask generator.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Language\MaskGenerator
     */
    protected $languageMaskGenerator;

    /**
     * Returns a language handler mock.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language\Handler
     */
    protected function getLanguageHandler()
    {
        if (!isset($this->languageHandler)) {
            $this->languageHandler = new LanguageHandlerMock();
        }

        return $this->languageHandler;
    }

    /**
     * Returns a language mask generator.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\Language\MaskGenerator
     */
    protected function getLanguageMaskGenerator()
    {
        if (!isset($this->languageMaskGenerator)) {
            $this->languageMaskGenerator = new LanguageMaskGenerator(
                $this->getLanguageHandler()
            );
        }

        return $this->languageMaskGenerator;
    }

    /**
     * Return definition-based transformation processor instance.
     *
     * @return Persistence\TransformationProcessor\DefinitionBased
     */
    protected function getDefinitionBasedTransformationProcessor()
    {
        return new Persistence\TransformationProcessor\DefinitionBased(
            new Persistence\TransformationProcessor\DefinitionBased\Parser(),
            new Persistence\TransformationProcessor\PcreCompiler(
                new Persistence\Utf8Converter()
            ),
            glob(__DIR__ . '/../../../../Persistence/Tests/TransformationProcessor/_fixtures/transformations/*.tr')
        );
    }

    /** @var \Ibexa\Core\Search\Common\FieldNameGenerator|\PHPUnit\Framework\MockObject\MockObject */
    protected $fieldNameGeneratorMock;

    /**
     * @return \Ibexa\Core\Search\Common\FieldNameGenerator|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getFieldNameGeneratorMock()
    {
        if (!isset($this->fieldNameGeneratorMock)) {
            $this->fieldNameGeneratorMock = $this->createMock(FieldNameGenerator::class);
        }

        return $this->fieldNameGeneratorMock;
    }

    /**
     * @param \Ibexa\Core\Persistence\Legacy\Content\Type\Handler $contentTypeHandler
     *
     * @return \Ibexa\Core\Search\Legacy\Content\Mapper\FullTextMapper
     */
    protected function getFullTextMapper(Persistence\Legacy\Content\Type\Handler $contentTypeHandler)
    {
        return new FullTextMapper(
            $this->createMock(FieldRegistry::class),
            $contentTypeHandler
        );
    }

    /**
     * Get FullText search configuration.
     */
    protected function getFullTextSearchConfiguration()
    {
        return [
            'stopWordThresholdFactor' => 0.66,
            'enableWildcards' => true,
            'commands' => [
                'apostrophe_normalize',
                'apostrophe_to_doublequote',
                'ascii_lowercase',
                'ascii_search_cleanup',
                'cyrillic_diacritical',
                'cyrillic_lowercase',
                'cyrillic_search_cleanup',
                'cyrillic_transliterate_ascii',
                'doublequote_normalize',
                'endline_search_normalize',
                'greek_diacritical',
                'greek_lowercase',
                'greek_normalize',
                'greek_transliterate_ascii',
                'hebrew_transliterate_ascii',
                'hyphen_normalize',
                'inverted_to_normal',
                'latin1_diacritical',
                'latin1_lowercase',
                'latin1_transliterate_ascii',
                'latin-exta_diacritical',
                'latin-exta_lowercase',
                'latin-exta_transliterate_ascii',
                'latin_lowercase',
                'latin_search_cleanup',
                'latin_search_decompose',
                'math_to_ascii',
                'punctuation_normalize',
                'space_normalize',
                'special_decompose',
                'specialwords_search_normalize',
                'tab_search_normalize',
            ],
        ];
    }
}

class_alias(LanguageAwareTestCase::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\LanguageAwareTestCase');
