<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Exceptions;

use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\Exception as RepositoryException;

/**
 * This Exception is thrown if the user has is not allowed to perform a service operation.
 */
abstract class UnauthorizedException extends Exception implements RepositoryException
{
}

class_alias(UnauthorizedException::class, 'eZ\Publish\API\Repository\Exceptions\UnauthorizedException');
