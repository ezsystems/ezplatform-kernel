<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class is used for updating location meta data.
 */
class LocationUpdateStruct extends ValueObject
{
    /**
     * If set the location priority is changed to the new value.
     *
     * @var int
     */
    public $priority;

    /**
     * If set the location gets a new remoteId.
     *
     * Needs to be a unique Location->remoteId string value.
     *
     * @var mixed
     */
    public $remoteId;

    /**
     * If set the sortField is changed.
     * The sort field specifies which property the child locations should be sorted on.
     * Valid values are found at {@link Location::SORT_FIELD_*}.
     *
     * @var mixed
     */
    public $sortField;

    /**
     * If set the sortOrder is changed.
     * The sort order specifies whether the sort order should be ascending or descending.
     * Valid values are {@link Location::SORT_ORDER_*}.
     *
     * @var mixed
     */
    public $sortOrder;
}

class_alias(LocationUpdateStruct::class, 'eZ\Publish\API\Repository\Values\Content\LocationUpdateStruct');
