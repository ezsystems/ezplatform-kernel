<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content;

use Ibexa\Contracts\Core\Persistence\Content\Language;
use Ibexa\Contracts\Core\Persistence\Content\Language\CreateStruct;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandler;

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
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language\CreateStruct $struct
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language
     */
    public function create(CreateStruct $struct)
    {
        throw new \RuntimeException('Not implemented yet.');
    }

    /**
     * Update language.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language $struct
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
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If language could not be found by $id
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language
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
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If language could not be found by $languageCode
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language
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
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language[]
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

class_alias(LanguageHandlerMock::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\LanguageHandlerMock');
