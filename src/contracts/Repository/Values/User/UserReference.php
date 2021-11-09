<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

/**
 *  This interface represents a user reference for use in sessions and Repository.
 */
interface UserReference
{
    /**
     * The User id of the User this reference represent.
     *
     * @return int
     */
    public function getUserId(): int;
}

class_alias(UserReference::class, 'eZ\Publish\API\Repository\Values\User\UserReference');
