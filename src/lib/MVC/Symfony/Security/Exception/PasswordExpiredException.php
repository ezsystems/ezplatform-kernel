<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Security\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

final class PasswordExpiredException extends CustomUserMessageAccountStatusException
{
    public function __construct(string $message = 'User account has expired.')
    {
        parent::__construct($message);
    }
}
