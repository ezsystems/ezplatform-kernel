<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Exceptions;

/**
 * This Exception is thrown if a method is called with an value referencing an object which is not in the right state.
 */
abstract class BadStateException extends ForbiddenException
{
}

class_alias(BadStateException::class, 'eZ\Publish\API\Repository\Exceptions\BadStateException');
