<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Exceptions;

use Ibexa\Contracts\Core\Repository\Exceptions\Exception as RepositoryException;
use OutOfBoundsException as BaseOutOfBoundsException;

class OutOfBoundsException extends BaseOutOfBoundsException implements RepositoryException
{
}

class_alias(OutOfBoundsException::class, 'eZ\Publish\API\Repository\Exceptions\OutOfBoundsException');
