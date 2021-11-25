<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\MVC\Symfony\Security;

use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\MVC\Symfony\Security\UserInterface as EzUserInterface;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof EzUserInterface) {
            return;
        }

        if (!$user->getAPIUser()->enabled) {
            $exception = new DisabledException('User account is locked.');
            $exception->setUser($user);

            throw $exception;
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof EzUserInterface) {
            return;
        }

        if ($this->userService->getPasswordInfo($user->getAPIUser())->isPasswordExpired()) {
            $exception = new CredentialsExpiredException('User account has expired.');
            $exception->setUser($user);

            throw $exception;
        }
    }
}
