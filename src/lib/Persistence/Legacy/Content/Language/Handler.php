<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Content\Language;

use Ibexa\Contracts\Core\Persistence\Content\Language;
use Ibexa\Contracts\Core\Persistence\Content\Language\CreateStruct;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as BaseLanguageHandler;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use LogicException;

/**
 * Language Handler.
 */
class Handler implements BaseLanguageHandler
{
    /**
     * Language Gateway.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Language\Gateway
     */
    protected $languageGateway;

    /**
     * Language Mapper.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Language\Mapper
     */
    protected $languageMapper;

    /**
     * Creates a new Language Handler.
     *
     * @param \Ibexa\Core\Persistence\Legacy\Content\Language\Gateway $languageGateway
     * @param \Ibexa\Core\Persistence\Legacy\Content\Language\Mapper $languageMapper
     */
    public function __construct(Gateway $languageGateway, Mapper $languageMapper)
    {
        $this->languageGateway = $languageGateway;
        $this->languageMapper = $languageMapper;
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
        $language = $this->languageMapper->createLanguageFromCreateStruct(
            $struct
        );
        $language->id = $this->languageGateway->insertLanguage($language);

        return $language;
    }

    /**
     * Update language.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language $language
     */
    public function update(Language $language)
    {
        $this->languageGateway->updateLanguage($language);
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
        $languages = $this->languageMapper->extractLanguagesFromRows(
            $this->languageGateway->loadLanguageListData([$id])
        );

        if (count($languages) < 1) {
            throw new NotFoundException('Language', $id);
        }

        return reset($languages);
    }

    /**
     * {@inheritdoc}
     */
    public function loadList(array $ids): iterable
    {
        return $this->languageMapper->extractLanguagesFromRows(
            $this->languageGateway->loadLanguageListData($ids),
            'id'
        );
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
        $languages = $this->languageMapper->extractLanguagesFromRows(
            $this->languageGateway->loadLanguageListDataByLanguageCode([$languageCode])
        );

        if (count($languages) < 1) {
            throw new NotFoundException('Language', $languageCode);
        }

        return reset($languages);
    }

    /**
     * {@inheritdoc}
     */
    public function loadListByLanguageCodes(array $languageCodes): iterable
    {
        return $this->languageMapper->extractLanguagesFromRows(
            $this->languageGateway->loadLanguageListDataByLanguageCode($languageCodes)
        );
    }

    /**
     * Get all languages.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language[]
     */
    public function loadAll()
    {
        return $this->languageMapper->extractLanguagesFromRows(
            $this->languageGateway->loadAllLanguagesData()
        );
    }

    /**
     * Delete a language.
     *
     * @param mixed $id
     *
     * @throws \LogicException If language could not be deleted
     */
    public function delete($id)
    {
        if (!$this->languageGateway->canDeleteLanguage($id)) {
            throw new LogicException('Cannot delete language: some content still references the language');
        }

        $this->languageGateway->deleteLanguage($id);
    }
}

class_alias(Handler::class, 'eZ\Publish\Core\Persistence\Legacy\Content\Language\Handler');
