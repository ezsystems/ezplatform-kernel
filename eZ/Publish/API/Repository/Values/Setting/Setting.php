<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Values\Setting;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * @property-read string $group
 * @property-read string $identifier
 * @property-read mixed $value
 */
class Setting extends ValueObject
{
    /** @var string */
    protected $group;

    /** @var string */
    protected $identifier;

    /** @var mixed */
    protected $value;
}
