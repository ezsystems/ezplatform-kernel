<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\User;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class RoleUpdateStruct extends ValueObject
{
    /**
     * ID of the user rule.
     *
     * @var mixed
     */
    public $id;

    /**
     * Identifier of the role.
     *
     * Legacy note: Maps to name in 4.x.
     *
     * @var string
     */
    public $identifier;

    /**
     * Name of the role.
     *
     * @since 5.0
     *
     * @var string[]
     */
    public $name;

    /**
     * Description of the role.
     *
     * @since 5.0
     *
     * @var string[]
     */
    public $description = [];
}

class_alias(RoleUpdateStruct::class, 'eZ\Publish\SPI\Persistence\User\RoleUpdateStruct');
