<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\API\Repository\Iterator;

use Iterator;

interface BatchIteratorAdapter
{
    public function fetch(int $offset, int $limit): Iterator;
}
