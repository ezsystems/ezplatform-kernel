<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\User;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class RoleCreateStruct extends ValueObject
{
    /**
     * Identifier of the role.
     *
     * Legacy note: Maps to name in 4.x.
     *
     * @var string
     */
    public $identifier;

    /**
     * Contains an array of role policies.
     *
     * @var mixed[]
     */
    public $policies = [];
}

class_alias(RoleCreateStruct::class, 'eZ\Publish\SPI\Persistence\User\RoleCreateStruct');
