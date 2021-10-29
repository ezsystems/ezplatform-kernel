<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Exceptions;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Throwable;

class PasswordInUnsupportedFormatException extends AuthenticationException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("User's password is in a format which is not supported any more.", 0, $previous);
    }
}

class_alias(PasswordInUnsupportedFormatException::class, 'eZ\Publish\API\Repository\Exceptions\PasswordInUnsupportedFormatException');
