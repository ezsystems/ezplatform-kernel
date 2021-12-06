<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\User;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroupCreateStruct;

final class CreateUserGroupEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroupCreateStruct */
    private $userGroupCreateStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup */
    private $parentGroup;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup */
    private $userGroup;

    public function __construct(
        UserGroup $userGroup,
        UserGroupCreateStruct $userGroupCreateStruct,
        UserGroup $parentGroup
    ) {
        $this->userGroupCreateStruct = $userGroupCreateStruct;
        $this->parentGroup = $parentGroup;
        $this->userGroup = $userGroup;
    }

    public function getUserGroupCreateStruct(): UserGroupCreateStruct
    {
        return $this->userGroupCreateStruct;
    }

    public function getParentGroup(): UserGroup
    {
        return $this->parentGroup;
    }

    public function getUserGroup(): UserGroup
    {
        return $this->userGroup;
    }
}

class_alias(CreateUserGroupEvent::class, 'eZ\Publish\API\Repository\Events\User\CreateUserGroupEvent');
