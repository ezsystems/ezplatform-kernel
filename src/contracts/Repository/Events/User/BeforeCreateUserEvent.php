<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\User;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserCreateStruct;
use UnexpectedValueException;

final class BeforeCreateUserEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserCreateStruct */
    private $userCreateStruct;

    /** @var array */
    private $parentGroups;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User|null */
    private $user;

    public function __construct(UserCreateStruct $userCreateStruct, array $parentGroups)
    {
        $this->userCreateStruct = $userCreateStruct;
        $this->parentGroups = $parentGroups;
    }

    public function getUserCreateStruct(): UserCreateStruct
    {
        return $this->userCreateStruct;
    }

    public function getParentGroups(): array
    {
        return $this->parentGroups;
    }

    public function getUser(): User
    {
        if (!$this->hasUser()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasUser() or set it using setUser() before you call the getter.', User::class));
        }

        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function hasUser(): bool
    {
        return $this->user instanceof User;
    }
}

class_alias(BeforeCreateUserEvent::class, 'eZ\Publish\API\Repository\Events\User\BeforeCreateUserEvent');
