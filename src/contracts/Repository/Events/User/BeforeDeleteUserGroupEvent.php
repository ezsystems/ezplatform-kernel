<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\User;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use UnexpectedValueException;

final class BeforeDeleteUserGroupEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup */
    private $userGroup;

    /** @var array|null */
    private $locations;

    public function __construct(UserGroup $userGroup)
    {
        $this->userGroup = $userGroup;
    }

    public function getUserGroup(): UserGroup
    {
        return $this->userGroup;
    }

    public function getLocations(): array
    {
        if (!$this->hasLocations()) {
            throw new UnexpectedValueException('Return value is not set or not a type of %s. Check hasLocations() or set it using setLocations() before you call getter.');
        }

        return $this->locations;
    }

    public function setLocations(?array $locations): void
    {
        $this->locations = $locations;
    }

    public function hasLocations(): bool
    {
        return is_array($this->locations);
    }
}

class_alias(BeforeDeleteUserGroupEvent::class, 'eZ\Publish\API\Repository\Events\User\BeforeDeleteUserGroupEvent');
