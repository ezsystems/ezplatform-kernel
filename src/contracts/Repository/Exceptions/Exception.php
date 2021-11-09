<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Exceptions;

use Throwable;

/**
 * Marker interface for all Repository related exceptions.
 */
interface Exception extends Throwable
{
}

class_alias(Exception::class, 'eZ\Publish\API\Repository\Exceptions\Exception');
