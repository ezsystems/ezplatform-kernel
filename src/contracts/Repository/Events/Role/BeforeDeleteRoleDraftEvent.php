<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Role;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\User\RoleDraft;

final class BeforeDeleteRoleDraftEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\RoleDraft */
    private $roleDraft;

    public function __construct(RoleDraft $roleDraft)
    {
        $this->roleDraft = $roleDraft;
    }

    public function getRoleDraft(): RoleDraft
    {
        return $this->roleDraft;
    }
}

class_alias(BeforeDeleteRoleDraftEvent::class, 'eZ\Publish\API\Repository\Events\Role\BeforeDeleteRoleDraftEvent');
