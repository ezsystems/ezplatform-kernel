<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Notification;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use IteratorAggregate;
use Traversable;

class NotificationList extends ValueObject implements IteratorAggregate
{
    /** @var int */
    public $totalCount = 0;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Notification\Notification[] */
    public $items = [];

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}

class_alias(NotificationList::class, 'eZ\Publish\API\Repository\Values\Notification\NotificationList');
