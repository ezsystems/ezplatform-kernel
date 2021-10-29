<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Content\UrlWildcard;

use Ibexa\Contracts\Core\Persistence\Content\UrlWildcard;

/**
 * UrlWildcard Gateway.
 *
 * @internal For internal use by Persistence Handlers.
 */
abstract class Gateway
{
    public const URL_WILDCARD_TABLE = 'ezurlwildcard';
    public const URL_WILDCARD_SEQ = 'ezurlwildcard_id_seq';

    /**
     * Insert the given UrlWildcard.
     */
    abstract public function insertUrlWildcard(UrlWildcard $urlWildcard): int;

    /**
     * Update the given UrlWildcard.
     */
    abstract public function updateUrlWildcard(
        int $id,
        string $sourceUrl,
        string $destinationUrl,
        bool $forward
    ): void;

    /**
     * Delete the UrlWildcard with given $id.
     */
    abstract public function deleteUrlWildcard(int $id): void;

    /**
     * Load an array with data about UrlWildcard with $id.
     */
    abstract public function loadUrlWildcardData(int $id): array;

    /**
     * Load an array with data about UrlWildcards (paged).
     */
    abstract public function loadUrlWildcardsData(int $offset = 0, int $limit = -1): array;

    /**
     * Load the UrlWildcard by source url $sourceUrl.
     */
    abstract public function loadUrlWildcardBySourceUrl(string $sourceUrl): array;
}

class_alias(Gateway::class, 'eZ\Publish\Core\Persistence\Legacy\Content\UrlWildcard\Gateway');
