<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Security\User;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Core\MVC\Symfony\Security\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

final class EmailProvider extends BaseProvider
{
    public function loadUserByUsername($user)
    {
        try {
            // SecurityContext always tries to authenticate anonymous users when checking granted access.
            // In that case $user is an instance of \Ibexa\Core\MVC\Symfony\Security\User.
            // We don't need to reload the user here.
            if ($user instanceof UserInterface) {
                return $user;
            }

            return $this->createSecurityUser(
                $this->userService->loadUserByEmail($user)
            );
        } catch (NotFoundException $e) {
            throw new UsernameNotFoundException($e->getMessage(), 0, $e);
        }
    }
}

class_alias(EmailProvider::class, 'eZ\Publish\Core\MVC\Symfony\Security\User\EmailProvider');
