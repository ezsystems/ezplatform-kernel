<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Persistence\Legacy\Tests\Content;

use eZ\Publish\SPI\Persistence\Content\Language;
use eZ\Publish\SPI\Persistence\Content\Language\CreateStruct;
use eZ\Publish\SPI\Persistence\Content\Language\Handler as LanguageHandler;

/**
 * Simple mock for a Language\Handler.
 */
class LanguageHandlerMock implements LanguageHandler
{
    protected $languages = [];

    public function __construct()
    {
        $this->languages[] = new Language(
            [
                'id' => 2,
                'languageCode' => 'eng-US',
                'name' => 'US english',
            ]
        );
        $this->languages[] = new Language(
            [
                'id' => 4,
                'languageCode' => 'eng-GB',
                'name' => 'British english',
            ]
        );
        $this->languages[] = new Language(
            [
                'id' => 8,
                'languageCode' => 'ger-DE',
                'name' => 'German',
            ]
        );
    }

    /**
     * Create a new language.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Language\CreateStruct $struct
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Language
     */
    public function create(CreateStruct $struct)
    {
        throw new \RuntimeException('Not implemented yet.');
    }

    /**
     * Update language.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Language $struct
     */
    public function update(Language $struct)
    {
        throw new \RuntimeException('Not implemented yet.');
    }

    /**
     * Get language by id.
     *
     * @param mixed $id
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException If language could not be found by $id
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Language
     */
    public function load($id)
    {
        foreach ($this->languages as $language) {
            if ($language->id == $id) {
                return $language;
            }
        }
        throw new \RuntimeException("Language $id not found.");
    }

    /**
     * Get language by Language Code (eg: eng-GB).
     *
     * @param string $languageCode
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException If language could not be found by $languageCode
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Language
     */
    public function loadByLanguageCode($languageCode)
    {
        foreach ($this->languages as $language) {
            if ($language->languageCode == $languageCode) {
                return $language;
            }
        }
        throw new \RuntimeException("Language $languageCode not found.");
    }

    /**
     * Get all languages.
     *
     * Return list of languages where key of hash is language code.
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Language[]
     */
    public function loadAll()
    {
        return $this->languages;
    }

    /**
     * Delete a language.
     *
     * @todo Might throw an exception if the language is still associated with some content / types / (...) ?
     *
     * @param mixed $id
     */
    public function delete($id)
    {
        throw new \RuntimeException('Not implemented yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function loadList(array $ids): iterable
    {
        $languages = [];
        foreach ($this->languages as $language) {
            if (in_array($language->id, $ids)) {
                $languages[$language->id] = $language;
            }
        }

        return $languages;
    }

    /**
     * {@inheritdoc}
     */
    public function loadListByLanguageCodes(array $languageCodes): iterable
    {
        $languages = [];
        foreach ($this->languages as $language) {
            if (in_array($language->languageCode, $languageCodes)) {
                $languages[$language->languageCode] = $language;
            }
        }

        return $languages;
    }
}
