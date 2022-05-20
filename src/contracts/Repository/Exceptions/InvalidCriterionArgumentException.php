<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Exceptions;

use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;

final class InvalidCriterionArgumentException extends InvalidArgumentException
{
    public function __construct($key, $criterion, string $expectedCriterionFQCN)
    {
        if ($criterion === null) {
            $type = 'null';
        } elseif (is_object($criterion)) {
            $type = get_class($criterion);
        } elseif (is_array($criterion)) {
            $type = 'Array, with keys: ' . implode(', ', array_keys($criterion));
        } else {
            $type = gettype($criterion) . ", with value: '{$criterion}'";
        }

        parent::__construct("You provided {$type} at index '{$key}', but only instances of '{$expectedCriterionFQCN}' are accepted");
    }
}
