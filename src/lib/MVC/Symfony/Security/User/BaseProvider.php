<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Security\User;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User as APIUser;
use Ibexa\Core\MVC\Symfony\Security\ReferenceUserInterface;
use Ibexa\Core\MVC\Symfony\Security\User;
use Ibexa\Core\MVC\Symfony\Security\UserInterface;
use Ibexa\Core\Repository\Values\User\UserReference;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as CoreUserInterface;

abstract class BaseProvider implements APIUserProviderInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    protected $userService;

    public function __construct(
        UserService $userService,
        PermissionResolver $permissionResolver
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
    }

    public function refreshUser(CoreUserInterface $user)
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        try {
            $refreshedAPIUser = $this->userService->loadUser(
                $user instanceof ReferenceUserInterface
                    ? $user->getAPIUserReference()->getUserId()
                    : $user->getAPIUser()->id
            );
            $user->setAPIUser($refreshedAPIUser);
            $this->permissionResolver->setCurrentUserReference(
                new UserReference($refreshedAPIUser->getUserId())
            );

            return $user;
        } catch (NotFoundException $e) {
            throw new UsernameNotFoundException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === UserInterface::class || is_subclass_of($class, UserInterface::class);
    }

    /**
     * Loads a regular user object, usable by Symfony Security component, from a user object returned by Public API.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\User $apiUser
     *
     * @return \Ibexa\Core\MVC\Symfony\Security\User
     */
    public function loadUserByAPIUser(APIUser $apiUser)
    {
        return $this->createSecurityUser($apiUser);
    }

    /**
     * Creates user object, usable by Symfony Security component, from a user object returned by Public API.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\User $apiUser
     *
     * @return \Ibexa\Core\MVC\Symfony\Security\User
     */
    protected function createSecurityUser(APIUser $apiUser): User
    {
        return new User($apiUser, ['ROLE_USER']);
    }
}

class_alias(BaseProvider::class, 'eZ\Publish\Core\MVC\Symfony\Security\User\BaseProvider');
