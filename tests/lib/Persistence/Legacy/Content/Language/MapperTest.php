<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\Language;

use Ibexa\Contracts\Core\Persistence\Content\Language;
use Ibexa\Contracts\Core\Persistence\Content\Language\CreateStruct;
use Ibexa\Core\Persistence\Legacy\Content\Language\Mapper;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\Language\Mapper
 */
class MapperTest extends TestCase
{
    public function testCreateLanguageFromCreateStruct()
    {
        $mapper = new Mapper();

        $createStruct = $this->getCreateStructFixture();

        $result = $mapper->createLanguageFromCreateStruct($createStruct);

        $this->assertStructsEqual(
            $this->getLanguageFixture(),
            $result,
            ['languageCode', 'name', 'isEnabled']
        );
    }

    public function testExtractLanguagesFromRows()
    {
        $mapper = new Mapper();

        $rows = $this->getRowsFixture();

        $result = $mapper->extractLanguagesFromRows($rows);

        $this->assertEquals(
            $this->getExtractReference(),
            $result
        );
    }

    /**
     * Returns a result rows fixture.
     *
     * @return string[][]
     */
    protected function getRowsFixture()
    {
        return [
            ['disabled' => '0', 'id' => '2', 'locale' => 'eng-US', 'name' => 'English (American)'],
            ['disabled' => '0', 'id' => '4', 'locale' => 'eng-GB', 'name' => 'English (United Kingdom)'],
        ];
    }

    /**
     * Returns reference for the extraction from rows.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language[]
     */
    protected function getExtractReference()
    {
        $langUs = new Language();
        $langUs->id = 2;
        $langUs->languageCode = 'eng-US';
        $langUs->name = 'English (American)';
        $langUs->isEnabled = true;

        $langGb = new Language();
        $langGb->id = 4;
        $langGb->languageCode = 'eng-GB';
        $langGb->name = 'English (United Kingdom)';
        $langGb->isEnabled = true;

        return ['eng-US' => $langUs, 'eng-GB' => $langGb];
    }

    /**
     * Returns a Language CreateStruct fixture.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language\CreateStruct
     */
    protected function getCreateStructFixture()
    {
        $struct = new CreateStruct();

        $struct->languageCode = 'de-DE';
        $struct->name = 'Deutsch (Deutschland)';
        $struct->isEnabled = true;

        return $struct;
    }

    /**
     * Returns a Language fixture.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language
     */
    protected function getLanguageFixture()
    {
        $struct = new Language();

        $struct->languageCode = 'de-DE';
        $struct->name = 'Deutsch (Deutschland)';
        $struct->isEnabled = true;

        return $struct;
    }
}

class_alias(MapperTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\Language\MapperTest');
