<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\Core\Repository\Values\User\PolicyUpdateStruct as APIPolicyUpdateStruct;

/**
 * This class is used for updating a policy. The limitations of the policy are replaced
 * with those which are added in instances of this class.
 *
 * @internal Meant for internal use by Repository, type hint against API instead.
 */
class PolicyUpdateStruct extends APIPolicyUpdateStruct
{
    /**
     * List of limitations added to policy.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation[]
     */
    protected $limitations = [];

    /**
     * Returns list of limitations added to policy.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Limitation[]
     */
    public function getLimitations(): iterable
    {
        return $this->limitations;
    }

    /**
     * Adds a limitation to the policy - if a Limitation exists with the same identifier
     * the existing limitation is replaced.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation $limitation
     */
    public function addLimitation(Limitation $limitation): void
    {
        $limitationIdentifier = $limitation->getIdentifier();
        $this->limitations[$limitationIdentifier] = $limitation;
    }
}

class_alias(PolicyUpdateStruct::class, 'eZ\Publish\Core\Repository\Values\User\PolicyUpdateStruct');
