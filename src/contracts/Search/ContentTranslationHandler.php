<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Search;

/**
 * The Search Content translation handler.
 */
interface ContentTranslationHandler
{
    /**
     * Deletes a translation content object from the index.
     */
    public function deleteTranslation(int $contentId, string $languageCode): void;
}

class_alias(ContentTranslationHandler::class, 'eZ\Publish\SPI\Search\ContentTranslationHandler');
