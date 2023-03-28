<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Cache;

/**
 * @internal
 */
interface CacheIndicesValidatorInterface
{
    /**
     * @param mixed $object
     */
    public function validate(string $keyPrefix, $object, callable $cacheIndices): void;
}
