<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;

/**
 * This class represents a user reference for use in sessions and Repository.
 *
 * @internal Meant for internal use by Repository, type hint against API object instead.
 */
class UserReference implements APIUserReference
{
    /** @var int */
    private $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * The User id of the User this reference represent.
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
}

class_alias(UserReference::class, 'eZ\Publish\Core\Repository\Values\User\UserReference');
