<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Content\Language;

use Ibexa\Contracts\Core\Persistence\Content\Language;
use Ibexa\Contracts\Core\Persistence\Content\Language\CreateStruct;
use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as BaseLanguageHandler;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Ibexa\Core\Persistence\Cache\InMemory\InMemoryCache;

/**
 * Language Handler.
 */
class CachingHandler implements BaseLanguageHandler
{
    private const LANGUAGE_IDENTIFIER = 'language';
    private const LANGUAGE_CODE_IDENTIFIER = 'language_code';
    private const LANGUAGE_LIST_IDENTIFIER = 'language_list';

    /**
     * Inner Language handler.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Language\Handler
     */
    protected $innerHandler;

    /**
     * Language cache.
     *
     * @var \Ibexa\Core\Persistence\Cache\InMemory\InMemoryCache
     */
    protected $cache;

    /** @var \Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface */
    protected $cacheIdentifierGenerator;

    /**
     * Creates a caching handler around $innerHandler.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language\Handler $innerHandler
     * @param \Ibexa\Core\Persistence\Cache\InMemory\InMemoryCache $cache
     * @param \Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface $cacheIdentifierGenerator
     */
    public function __construct(
        BaseLanguageHandler $innerHandler,
        InMemoryCache $cache,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator
    ) {
        $this->innerHandler = $innerHandler;
        $this->cache = $cache;
        $this->cacheIdentifierGenerator = $cacheIdentifierGenerator;
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
        $language = $this->innerHandler->create($struct);
        $this->storeCache([$language]);

        return $language;
    }

    /**
     * Update language.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language $language
     */
    public function update(Language $language)
    {
        $this->innerHandler->update($language);
        $this->storeCache([$language]);
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
        $language = $this->cache->get(
            $this->cacheIdentifierGenerator->generateKey(self::LANGUAGE_IDENTIFIER, [$id], true)
        );

        if ($language === null) {
            $language = $this->innerHandler->load($id);
            $this->storeCache([$language]);
        }

        return $language;
    }

    /**
     * {@inheritdoc}
     */
    public function loadList(array $ids): iterable
    {
        $missing = [];
        $languages = [];
        foreach ($ids as $id) {
            if ($language = $this->cache->get($this->cacheIdentifierGenerator->generateKey(self::LANGUAGE_IDENTIFIER, [$id], true))) {
                $languages[$id] = $language;
            } else {
                $missing[] = $id;
            }
        }

        if (!empty($missing)) {
            $loaded = $this->innerHandler->loadList($missing);
            $this->storeCache($loaded);
            /** @noinspection AdditionOperationOnArraysInspection */
            $languages += $loaded;
        }

        // order languages by ID again so the result is deterministic regardless of cache
        // note: can't yield due to array access of this result
        $orderedLanguages = [];
        foreach ($ids as $id) {
            // BC: missing IDs are skipped
            if (!isset($languages[$id])) {
                continue;
            }

            $orderedLanguages[$id] = $languages[$id];
        }

        return $orderedLanguages;
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
        $language = $this->cache->get(
            $this->cacheIdentifierGenerator->generateKey(self::LANGUAGE_CODE_IDENTIFIER, [$languageCode], true)
        );

        if ($language === null) {
            $language = $this->innerHandler->loadByLanguageCode($languageCode);
            $this->storeCache([$language]);
        }

        return $language;
    }

    /**
     * {@inheritdoc}
     */
    public function loadListByLanguageCodes(array $languageCodes): iterable
    {
        $missing = [];
        $languages = [];
        foreach ($languageCodes as $languageCode) {
            if ($language = $this->cache->get($this->cacheIdentifierGenerator->generateKey(self::LANGUAGE_CODE_IDENTIFIER, [$languageCode], true))) {
                $languages[$languageCode] = $language;
            } else {
                $missing[] = $languageCode;
            }
        }

        if (!empty($missing)) {
            $loaded = $this->innerHandler->loadListByLanguageCodes($missing);
            $this->storeCache($loaded);
            /** @noinspection AdditionOperationOnArraysInspection */
            $languages += $loaded;
        }

        return $languages;
    }

    /**
     * Get all languages.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language[]
     */
    public function loadAll()
    {
        $languageListKey = $this->cacheIdentifierGenerator->generateKey(self::LANGUAGE_LIST_IDENTIFIER, [], true);
        $languages = $this->cache->get($languageListKey);

        if ($languages === null) {
            $languages = $this->innerHandler->loadAll();
            $this->storeCache($languages, $languageListKey);
        }

        return $languages;
    }

    /**
     * Delete a language.
     *
     * @param mixed $id
     */
    public function delete($id)
    {
        $this->innerHandler->delete($id);
        // Delete by primary key will remove the object, so we don't need to clear `ez-language-code-` here.
        $this->cache->deleteMulti([
            $this->cacheIdentifierGenerator->generateKey(self::LANGUAGE_IDENTIFIER, [$id], true),
            $this->cacheIdentifierGenerator->generateKey(self::LANGUAGE_LIST_IDENTIFIER, [], true),
        ]);
    }

    /**
     * Clear internal in-memory cache.
     */
    public function clearCache(): void
    {
        $this->cache->clear();
    }

    /**
     * Helper to store languages in internal in-memory cache with all needed keys.
     *
     * @param array $languages
     * @param string|null $listIndex
     */
    protected function storeCache(array $languages, string $listIndex = null): void
    {
        $generator = $this->cacheIdentifierGenerator;

        $this->cache->setMulti(
            $languages,
            static function (Language $language) use ($generator) {
                return [
                    $generator->generateKey(self::LANGUAGE_IDENTIFIER, [$language->id], true),
                    $generator->generateKey(self::LANGUAGE_CODE_IDENTIFIER, [$language->languageCode], true),
                ];
            },
            $listIndex
        );
    }
}

class_alias(CachingHandler::class, 'eZ\Publish\Core\Persistence\Legacy\Content\Language\CachingHandler');
