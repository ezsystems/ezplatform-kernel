<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content;

/**
 * this class represents a trash item, which is actually a trashed location.
 */
abstract class TrashItem extends Location
{
    /**
     * Trashed timestamp.
     *
     * @var \DateTime
     */
    protected $trashed;
}

class_alias(TrashItem::class, 'eZ\Publish\API\Repository\Values\Content\TrashItem');
