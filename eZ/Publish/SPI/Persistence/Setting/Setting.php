<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\SPI\Persistence\Setting;

use eZ\Publish\SPI\Persistence\ValueObject;

/**
 * @property-read string $group
 * @property-read string $identifier
 * @property-read string $serializedValue
 */
class Setting extends ValueObject
{
    /** @var string */
    protected $group;

    /** @var string */
    protected $identifier;

    /** @var string */
    protected $serializedValue;
}
