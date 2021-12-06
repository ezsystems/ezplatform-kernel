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
 * This Exception is thrown if an object referenced by an id or identifier
 * could not be found in the repository.
 */
abstract class NotFoundException extends Exception implements RepositoryException
{
}

class_alias(NotFoundException::class, 'eZ\Publish\API\Repository\Exceptions\NotFoundException');
