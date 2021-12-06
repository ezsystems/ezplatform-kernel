<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Role;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\Policy;

final class DeletePolicyEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Policy */
    private $policy;

    public function __construct(
        Policy $policy
    ) {
        $this->policy = $policy;
    }

    public function getPolicy(): Policy
    {
        return $this->policy;
    }
}

class_alias(DeletePolicyEvent::class, 'eZ\Publish\API\Repository\Events\Role\DeletePolicyEvent');
