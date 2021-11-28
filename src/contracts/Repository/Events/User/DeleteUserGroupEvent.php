<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\User;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;

final class DeleteUserGroupEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup */
    private $userGroup;

    /** @var array */
    private $locations;

    public function __construct(
        array $locations,
        UserGroup $userGroup
    ) {
        $this->userGroup = $userGroup;
        $this->locations = $locations;
    }

    public function getUserGroup(): UserGroup
    {
        return $this->userGroup;
    }

    public function getLocations(): array
    {
        return $this->locations;
    }
}

class_alias(DeleteUserGroupEvent::class, 'eZ\Publish\API\Repository\Events\User\DeleteUserGroupEvent');
