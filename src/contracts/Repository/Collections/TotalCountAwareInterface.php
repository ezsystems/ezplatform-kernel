<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Collections;

interface TotalCountAwareInterface
{
    /**
     * Get a total number of items matched by criteria, regardless of slice (page, collection) size.
     */
    public function getTotalCount(): int;
}
