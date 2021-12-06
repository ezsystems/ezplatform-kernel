<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

/**
 * Class ObjectStateLimitation.
 *
 * This Object state serves as API limitation for "StateGroup" from legacy,
 * StateGroup stored a combination of StateGroup identifier as well as State id(s) while this one
 * only cares about the state id's.
 */
class ObjectStateLimitation extends Limitation
{
    /**
     * @see \Ibexa\Contracts\Core\Repository\Values\User\Limitation::getIdentifier()
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return Limitation::STATE;
    }
}

class_alias(ObjectStateLimitation::class, 'eZ\Publish\API\Repository\Values\User\Limitation\ObjectStateLimitation');
