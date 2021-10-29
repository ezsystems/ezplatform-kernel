<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

class ParentContentTypeLimitation extends Limitation
{
    /**
     * @see \Ibexa\Contracts\Core\Repository\Values\User\Limitation::getIdentifier()
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return Limitation::PARENTCONTENTTYPE;
    }
}

class_alias(ParentContentTypeLimitation::class, 'eZ\Publish\API\Repository\Values\User\Limitation\ParentContentTypeLimitation');
