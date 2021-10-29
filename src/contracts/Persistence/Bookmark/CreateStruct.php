<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Persistence\Bookmark;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class CreateStruct extends ValueObject
{
    /**
     * Name of the bookmarked location.
     *
     * @deprecated Property is here purely for BC with 5.x.
     *
     * @var string
     */
    public $name;

    /**
     * ID of the bookmarked Location.
     *
     * @var int
     */
    public $locationId;

    /**
     * ID of bookmark owner.
     *
     * @var int
     */
    public $userId;
}

class_alias(CreateStruct::class, 'eZ\Publish\SPI\Persistence\Bookmark\CreateStruct');
