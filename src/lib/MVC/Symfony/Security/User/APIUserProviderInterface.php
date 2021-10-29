<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Security\User;

use Ibexa\Contracts\Core\Repository\Values\User\User as APIUser;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Interface adding eZ Publish API specific methods to Symfony UserProviderInterface.
 */
interface APIUserProviderInterface extends UserProviderInterface
{
    /**
     * Loads a regular user object, usable by Symfony Security component, from a user object returned by Public API.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\User $apiUser
     *
     * @return \Ibexa\Core\MVC\Symfony\Security\User
     */
    public function loadUserByAPIUser(APIUser $apiUser);
}

class_alias(APIUserProviderInterface::class, 'eZ\Publish\Core\MVC\Symfony\Security\User\APIUserProviderInterface');
