<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\User;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserUpdateStruct;
use UnexpectedValueException;

final class BeforeUpdateUserEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $user;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserUpdateStruct */
    private $userUpdateStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User|null */
    private $updatedUser;

    public function __construct(User $user, UserUpdateStruct $userUpdateStruct)
    {
        $this->user = $user;
        $this->userUpdateStruct = $userUpdateStruct;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserUpdateStruct(): UserUpdateStruct
    {
        return $this->userUpdateStruct;
    }

    public function getUpdatedUser(): User
    {
        if (!$this->hasUpdatedUser()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasUpdatedUser() or set it using setUpdatedUser() before you call the getter.', User::class));
        }

        return $this->updatedUser;
    }

    public function setUpdatedUser(?User $updatedUser): void
    {
        $this->updatedUser = $updatedUser;
    }

    public function hasUpdatedUser(): bool
    {
        return $this->updatedUser instanceof User;
    }
}

class_alias(BeforeUpdateUserEvent::class, 'eZ\Publish\API\Repository\Events\User\BeforeUpdateUserEvent');
