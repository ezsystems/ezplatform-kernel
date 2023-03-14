<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Cache;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
final class CacheIndicesValidator implements CacheIndicesValidatorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(?LoggerInterface $logger = null) {
        $this->logger = $logger;
    }

    /**
     * @param mixed $object
     */
    public function validate(string $keyPrefix, $object, callable $cacheIndices): void
    {
        if ($this->logger === null) {
            return;
        }

        $cacheIndicesUnpacked = $cacheIndices($object);

        foreach ($cacheIndicesUnpacked as $cacheIndex) {
            if (strpos($cacheIndex, $keyPrefix) === 0) {
                return;
            }
        }

        $this->logger->error(
            sprintf(
                'There is no corresponding cache index for key prefix %s. Cache indices are as follows: %s.',
                $keyPrefix,
                implode(', ', $cacheIndicesUnpacked)
            )
        );
    }
}
