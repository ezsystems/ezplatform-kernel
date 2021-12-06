<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\User;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserUpdateStruct;

final class UpdateUserEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $user;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserUpdateStruct */
    private $userUpdateStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $updatedUser;

    public function __construct(
        User $updatedUser,
        User $user,
        UserUpdateStruct $userUpdateStruct
    ) {
        $this->user = $user;
        $this->userUpdateStruct = $userUpdateStruct;
        $this->updatedUser = $updatedUser;
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
        return $this->updatedUser;
    }
}

class_alias(UpdateUserEvent::class, 'eZ\Publish\API\Repository\Events\User\UpdateUserEvent');
