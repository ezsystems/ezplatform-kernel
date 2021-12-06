<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Repository\SiteAccessAware;

use Ibexa\Contracts\Core\Repository\LanguageService as LanguageServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\LanguageCreateStruct;

/**
 * LanguageService for SiteAccessAware layer.
 *
 * Currently does nothing but hand over calls to aggregated service.
 */
class LanguageService implements LanguageServiceInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\LanguageService */
    protected $service;

    /**
     * Construct service object from aggregated service.
     *
     * @param \Ibexa\Contracts\Core\Repository\LanguageService $service
     */
    public function __construct(
        LanguageServiceInterface $service
    ) {
        $this->service = $service;
    }

    public function createLanguage(LanguageCreateStruct $languageCreateStruct): Language
    {
        return $this->service->createLanguage($languageCreateStruct);
    }

    public function updateLanguageName(Language $language, string $newName): Language
    {
        return $this->service->updateLanguageName($language, $newName);
    }

    public function enableLanguage(Language $language): Language
    {
        return $this->service->enableLanguage($language);
    }

    public function disableLanguage(Language $language): Language
    {
        return $this->service->disableLanguage($language);
    }

    public function loadLanguage(string $languageCode): Language
    {
        return $this->service->loadLanguage($languageCode);
    }

    public function loadLanguages(): iterable
    {
        return $this->service->loadLanguages();
    }

    public function loadLanguageById(int $languageId): Language
    {
        return $this->service->loadLanguageById($languageId);
    }

    public function loadLanguageListByCode(array $languageCodes): iterable
    {
        return $this->service->loadLanguageListByCode($languageCodes);
    }

    public function loadLanguageListById(array $languageIds): iterable
    {
        return $this->service->loadLanguageListById($languageIds);
    }

    public function deleteLanguage(Language $language): void
    {
        $this->service->deleteLanguage($language);
    }

    public function getDefaultLanguageCode(): string
    {
        return $this->service->getDefaultLanguageCode();
    }

    public function newLanguageCreateStruct(): LanguageCreateStruct
    {
        return $this->service->newLanguageCreateStruct();
    }
}

class_alias(LanguageService::class, 'eZ\Publish\Core\Repository\SiteAccessAware\LanguageService');
