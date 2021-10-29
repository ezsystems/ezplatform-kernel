<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Security;

use Ibexa\Contracts\Core\Repository\Values\User\User as APIUser;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

/**
 * Interface for Repository based users.
 */
interface UserInterface extends BaseUserInterface
{
    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\User\User
     */
    public function getAPIUser();

    /**
     * @deprecated Will be replaced by {@link ReferenceUserInterface::getAPIUser()}, adding LogicException to signature.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\User $apiUser
     */
    public function setAPIUser(APIUser $apiUser);
}

class_alias(UserInterface::class, 'eZ\Publish\Core\MVC\Symfony\Security\UserInterface');
