<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

/**
 * Status Limitation is used to limit the access to Content based on its version status.
 */
class StatusLimitation extends Limitation
{
    /**
     * @see \Ibexa\Contracts\Core\Repository\Values\User\Limitation::getIdentifier()
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return Limitation::STATUS;
    }
}

class_alias(StatusLimitation::class, 'eZ\Publish\API\Repository\Values\User\Limitation\StatusLimitation');
