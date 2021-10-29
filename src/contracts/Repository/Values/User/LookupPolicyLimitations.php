<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class represents a result of lookup limitation for module and function in the context of current User.
 */
final class LookupPolicyLimitations extends ValueObject
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Policy */
    protected $policy;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation[] */
    protected $limitations;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Policy $policy
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation[] $limitations
     */
    public function __construct(Policy $policy, array $limitations = [])
    {
        parent::__construct();

        $this->policy = $policy;
        $this->limitations = $limitations;
    }
}

class_alias(LookupPolicyLimitations::class, 'eZ\Publish\API\Repository\Values\User\LookupPolicyLimitations');
