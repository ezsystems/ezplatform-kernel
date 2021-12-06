<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class represents a LookupLimitation for module and function in the context of current User.
 */
final class LookupLimitationResult extends ValueObject
{
    /** @var bool */
    protected $hasAccess;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation[] */
    protected $roleLimitations;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\LookupPolicyLimitations[] */
    protected $lookupPolicyLimitations;

    /**
     * @param bool $hasAccess
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation[] $roleLimitations
     * @param \Ibexa\Contracts\Core\Repository\Values\User\LookupPolicyLimitations[] $lookupPolicyLimitations
     */
    public function __construct(
        bool $hasAccess,
        array $roleLimitations = [],
        array $lookupPolicyLimitations = []
    ) {
        parent::__construct();

        $this->hasAccess = $hasAccess;
        $this->lookupPolicyLimitations = $lookupPolicyLimitations;
        $this->roleLimitations = $roleLimitations;
    }
}

class_alias(LookupLimitationResult::class, 'eZ\Publish\API\Repository\Values\User\LookupLimitationResult');
