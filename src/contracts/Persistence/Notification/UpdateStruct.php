<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Persistence\Notification;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class UpdateStruct extends ValueObject
{
    /** @var bool */
    public $isPending;
}

class_alias(UpdateStruct::class, 'eZ\Publish\SPI\Persistence\Notification\UpdateStruct');
