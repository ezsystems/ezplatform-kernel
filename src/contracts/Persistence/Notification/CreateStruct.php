<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Persistence\Notification;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class CreateStruct extends ValueObject
{
    /** @var int */
    public $ownerId;

    /** @var string */
    public $type;

    /** @var bool */
    public $isPending;

    /** @var array */
    public $data = [];

    /** @var int */
    public $created;
}

class_alias(CreateStruct::class, 'eZ\Publish\SPI\Persistence\Notification\CreateStruct');
