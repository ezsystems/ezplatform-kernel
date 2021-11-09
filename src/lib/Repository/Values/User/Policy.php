<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\User\Policy as APIPolicy;

/**
 * This class represents a policy value.
 *
 * @internal Meant for internal use by Repository, type hint against API object instead.
 */
class Policy extends APIPolicy
{
    /**
     * Limitations assigned to this policy.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation[]
     */
    protected $limitations = [];

    /**
     * Returns the list of limitations for this policy.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Limitation[]
     */
    public function getLimitations(): iterable
    {
        return $this->limitations;
    }
}

class_alias(Policy::class, 'eZ\Publish\Core\Repository\Values\User\Policy');
