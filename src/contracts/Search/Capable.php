<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Search;

/**
 * Capability interface for search engines needed for {@see \Ibexa\Contracts\Core\Repository\SearchService::supports()}.
 *
 * @since 6.12 And ported to 6.7.6 for search engine forward compatibility.
 */
interface Capable
{
    /**
     * Query for supported capability of currently configured search engine.
     *
     * @param int $capabilityFlag One of \Ibexa\Contracts\Core\Repository\SearchService::CAPABILITY_* constants.
     *
     * @return bool
     */
    public function supports(int $capabilityFlag): bool;
}

class_alias(Capable::class, 'eZ\Publish\SPI\Search\Capable');
