<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\User;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\User;

final class DeleteUserEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $user;

    /** @var array */
    private $locations;

    public function __construct(
        array $locations,
        User $user
    ) {
        $this->user = $user;
        $this->locations = $locations;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getLocations(): array
    {
        return $this->locations;
    }
}

class_alias(DeleteUserEvent::class, 'eZ\Publish\API\Repository\Events\User\DeleteUserEvent');
