<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\User;

use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\SPI\Repository\Event\BeforeEvent;
use UnexpectedValueException;

final class BeforeUpdateUserPasswordEvent extends BeforeEvent
{
    /** @var \eZ\Publish\API\Repository\Values\User\User */
    private $user;

    /** @var string */
    private $password;

    /** @var \eZ\Publish\API\Repository\Values\User\User|null */
    private $updatedUser;

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
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
