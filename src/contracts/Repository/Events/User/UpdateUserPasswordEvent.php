<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\User;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\User;

final class UpdateUserPasswordEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $user;

    /** @var string */
    private $newPassword;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $updatedUser;

    public function __construct(
        User $updatedUser,
        User $user,
        string $newPassword
    ) {
        $this->user = $user;
        $this->newPassword = $newPassword;
        $this->updatedUser = $updatedUser;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function getUpdatedUser(): User
    {
        return $this->updatedUser;
    }
}

class_alias(UpdateUserPasswordEvent::class, 'eZ\Publish\API\Repository\Events\User\UpdateUserPasswordEvent');
