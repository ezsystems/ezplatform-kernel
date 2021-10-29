<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Content\Language;

use Ibexa\Contracts\Core\Persistence\Content\Language;
use Ibexa\Contracts\Core\Persistence\Content\Language\CreateStruct;

/**
 * Language Mapper.
 */
class Mapper
{
    /**
     * Creates a Language from $struct.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Language\CreateStruct $struct
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language
     */
    public function createLanguageFromCreateStruct(CreateStruct $struct): Language
    {
        $language = new Language();

        $language->languageCode = $struct->languageCode;
        $language->name = $struct->name;
        $language->isEnabled = $struct->isEnabled;

        return $language;
    }

    /**
     * Extracts Language objects from $rows.
     *
     * @param array $rows
     * @param string $key Column name for use as key in returned array.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language[]
     */
    public function extractLanguagesFromRows(array $rows, string $key = 'locale'): array
    {
        $languages = [];

        foreach ($rows as $row) {
            $language = new Language();

            $language->id = (int)$row['id'];
            $language->languageCode = $row['locale'];
            $language->name = $row['name'];
            $language->isEnabled = !((int)$row['disabled']);

            $languages[$row[$key]] = $language;
        }

        return $languages;
    }
}

class_alias(Mapper::class, 'eZ\Publish\Core\Persistence\Legacy\Content\Language\Mapper');
