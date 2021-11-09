<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Exceptions;

/**
 * This exception is thrown if a service method is called with an illegal or non appropriate value.
 */
abstract class InvalidArgumentException extends ForbiddenException
{
}

class_alias(InvalidArgumentException::class, 'eZ\Publish\API\Repository\Exceptions\InvalidArgumentException');
