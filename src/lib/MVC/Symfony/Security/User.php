<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Security;

use Ibexa\Contracts\Core\Repository\Values\User\User as APIUser;
use Ibexa\Core\Repository\Values\User\UserReference;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

class User implements ReferenceUserInterface, EquatableInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $user;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserReference */
    private $reference;

    /** @var string[] */
    private $roles;

    public function __construct(APIUser $user, array $roles = [])
    {
        $this->user = $user;
        $this->reference = new UserReference($user->getUserId());
        $this->roles = $roles;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array( 'ROLE_USER' );
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return string[] The user roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->getAPIUser()->passwordHash;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getAPIUser()->login;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\User\UserReference
     */
    public function getAPIUserReference()
    {
        return $this->reference;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\User\User
     */
    public function getAPIUser()
    {
        if (!$this->user instanceof APIUser) {
            throw new \LogicException(
                'Attempted to get APIUser before it has been set by UserProvider, APIUser is not serialized to session'
            );
        }

        return $this->user;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\User\User $user
     */
    public function setAPIUser(APIUser $user)
    {
        $this->user = $user;
        $this->reference = new UserReference($user->getUserId());
    }

    public function isEqualTo(BaseUserInterface $user)
    {
        // Check for the lighter ReferenceUserInterface first
        if ($user instanceof ReferenceUserInterface) {
            return $user->getAPIUserReference()->getUserId() === $this->reference->getUserId();
        } elseif ($user instanceof UserInterface) {
            return $user->getAPIUser()->getUserId() === $this->reference->getUserId();
        }

        return false;
    }

    public function __toString()
    {
        return $this->getAPIUser()->contentInfo->name;
    }

    /**
     * Make sure we don't serialize the whole API user object given it's a full fledged api content object. We set
     * (& either way refresh) the user object in {@see \Ibexa\Core\MVC\Symfony\Security\User\BaseProvider::refreshUser}
     * when object wakes back up from session.
     *
     * @return array
     */
    public function __sleep()
    {
        return ['reference', 'roles'];
    }
}

class_alias(User::class, 'eZ\Publish\Core\MVC\Symfony\Security\User');
