<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Persistence\UserPreference;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class UserPreference extends ValueObject
{
    /**
     * ID of the user preference.
     *
     * @var int
     */
    public $id;

    /**
     * The ID of the user this user preference belongs to.
     *
     * @var int
     */
    public $userId;

    /**
     * Name of user preference.
     *
     * Eg: timezone
     *
     * @var string
     */
    public $name;

    /**
     * Value of user preference.
     *
     * Eg: America/New_York
     *
     * @var string
     */
    public $value;
}

class_alias(UserPreference::class, 'eZ\Publish\SPI\Persistence\UserPreference\UserPreference');
