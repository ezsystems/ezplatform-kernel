<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\UserPreference;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class UserPreferenceSetStruct extends ValueObject
{
    /** @var string */
    public $name;

    /** @var string */
    public $value;
}

class_alias(UserPreferenceSetStruct::class, 'eZ\Publish\API\Repository\Values\UserPreference\UserPreferenceSetStruct');
