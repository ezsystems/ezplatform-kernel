<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Mapper\ContentLocationMapper;

/**
 * Retrieves Content ID from Location ID.
 *
 * Helps in scenarios where you need to retrieve Content IDs from
 * a large location list. You'd normally do LocationService::loadLocation call
 * for every Location ID which requires a lot of memory.
 *
 * It works by tracing all loadLocation calls via Service\LocationService decorator
 * and updating the map accordingly. Currently, in memory cache mechanism is used but
 * can be further optimized by implementing persistence cache.
 *
 * @internal For internal use by Ibexa packages only
 */
final class InMemoryContentLocationMapper implements ContentLocationMapper
{
    /** @var array<int, int> */
    private $map;

    /**
     * @param int[] $map
     */
    public function __construct(array $map = [])
    {
        $this->map = $map;
    }

    public function hasMapping(int $locationId): bool
    {
        return isset($this->map[$locationId]);
    }

    public function getMapping(int $locationId): int
    {
        return $this->map[$locationId];
    }

    public function setMapping(int $locationId, int $contentId): void
    {
        $this->map[$locationId] = $contentId;
    }

    public function removeMapping(int $locationId): void
    {
        unset($this->map[$locationId]);
    }
}
