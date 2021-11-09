<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Exceptions;

use Exception;

/**
 * This Exception is thrown if a feature has not been implemented
 * _intentionally_. The main purpose is the search handler, where some features
 * are just not supported in the legacy search implementation.
 */
class NotImplementedException extends ForbiddenException
{
    /**
     * Generates: Intentionally not implemented: {$feature}.
     *
     * @param string $feature
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($feature, $code = 0, Exception $previous = null)
    {
        parent::__construct("Intentionally not implemented: {$feature}", $code, $previous);
    }
}

class_alias(NotImplementedException::class, 'eZ\Publish\API\Repository\Exceptions\NotImplementedException');
