<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Security\Authentication;

use Ibexa\Contracts\Core\Repository\Exceptions\PasswordInUnsupportedFormatException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Core\MVC\Symfony\Security\UserInterface as EzUserInterface;
use Ibexa\Core\Repository\User\Exception\UnsupportedPasswordHashType;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;

class RepositoryAuthenticationProvider extends DaoAuthenticationProvider
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    public function setPermissionResolver(PermissionResolver $permissionResolver)
    {
        $this->permissionResolver = $permissionResolver;
    }

    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {
        if (!$user instanceof EzUserInterface) {
            parent::checkAuthentication($user, $token);

            return;
        }

        $apiUser = $user->getAPIUser();

        // $currentUser can either be an instance of UserInterface or just the username (e.g. during form login).
        /** @var \Ibexa\Core\MVC\Symfony\Security\UserInterface|string $currentUser */
        $currentUser = $token->getUser();
        if ($currentUser instanceof UserInterface) {
            if ($currentUser->getAPIUser()->passwordHash !== $apiUser->passwordHash) {
                throw new BadCredentialsException('The credentials were changed in another session.');
            }

            $apiUser = $currentUser->getAPIUser();
        } else {
            $credentialsValid = $this->userService->checkUserCredentials($apiUser, $token->getCredentials());

            if (!$credentialsValid) {
                throw new BadCredentialsException('Invalid credentials', 0);
            }
        }

        // Finally inject current user in the Repository
        $this->permissionResolver->setCurrentUserReference($apiUser);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\PasswordInUnsupportedFormatException
     */
    public function authenticate(TokenInterface $token)
    {
        try {
            return parent::authenticate($token);
        } catch (UnsupportedPasswordHashType $exception) {
            throw new PasswordInUnsupportedFormatException($exception);
        }
    }
}

class_alias(RepositoryAuthenticationProvider::class, 'eZ\Publish\Core\MVC\Symfony\Security\Authentication\RepositoryAuthenticationProvider');
