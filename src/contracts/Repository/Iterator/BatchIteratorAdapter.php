<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Iterator;

use Iterator;

interface BatchIteratorAdapter
{
    public function fetch(int $offset, int $limit): Iterator;
}

class_alias(BatchIteratorAdapter::class, 'eZ\Publish\API\Repository\Iterator\BatchIteratorAdapter');
