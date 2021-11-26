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
 * An Exception which is thrown if an operation cannot be performed by a service
 * although the current user would have permission to perform this action.
 */
abstract class ForbiddenException extends Exception implements RepositoryException
{
}

class_alias(ForbiddenException::class, 'eZ\Publish\API\Repository\Exceptions\ForbiddenException');
