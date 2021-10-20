<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository\ContentLocationMapper;

/**
 * @internal For internal use by Ibexa packages only
 */
interface ContentLocationMapper
{
    public function hasMapping(int $locationId): bool;

    public function getMapping(int $locationId): int;

    public function setMapping(int $locationId, int $contentId): void;

    public function removeMapping(int $locationId): void;
}
