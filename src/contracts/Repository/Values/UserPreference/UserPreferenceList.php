<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\UserPreference;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use IteratorAggregate;

/**
 * List of user preferences.
 */
class UserPreferenceList extends ValueObject implements IteratorAggregate
{
    /**
     * The total number of user preferences.
     *
     * @var int
     */
    public $totalCount = 0;

    /**
     * List of user preferences.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreference[]
     */
    public $items = [];

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->items);
    }
}

class_alias(UserPreferenceList::class, 'eZ\Publish\API\Repository\Values\UserPreference\UserPreferenceList');
