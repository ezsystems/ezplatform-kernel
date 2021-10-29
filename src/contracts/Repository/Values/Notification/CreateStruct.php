<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Notification;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class CreateStruct extends ValueObject
{
    /** @var int */
    public $ownerId;

    /** @var string */
    public $type;

    /** @var bool */
    public $isPending = true;

    /** @var array */
    public $data = [];
}

class_alias(CreateStruct::class, 'eZ\Publish\API\Repository\Values\Notification\CreateStruct');
