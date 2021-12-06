<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\User;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroupUpdateStruct;

final class UpdateUserGroupEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup */
    private $userGroup;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroupUpdateStruct */
    private $userGroupUpdateStruct;

    private $updatedUserGroup;

    public function __construct(
        UserGroup $updatedUserGroup,
        UserGroup $userGroup,
        UserGroupUpdateStruct $userGroupUpdateStruct
    ) {
        $this->userGroup = $userGroup;
        $this->userGroupUpdateStruct = $userGroupUpdateStruct;
        $this->updatedUserGroup = $updatedUserGroup;
    }

    public function getUserGroup(): UserGroup
    {
        return $this->userGroup;
    }

    public function getUserGroupUpdateStruct(): UserGroupUpdateStruct
    {
        return $this->userGroupUpdateStruct;
    }

    public function getUpdatedUserGroup(): UserGroup
    {
        return $this->updatedUserGroup;
    }
}

class_alias(UpdateUserGroupEvent::class, 'eZ\Publish\API\Repository\Events\User\UpdateUserGroupEvent');
