<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\User;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class Role extends ValueObject
{
    /** @var int Status constant for defined (aka "published") role */
    public const STATUS_DEFINED = 0;

    /** @var int Status constant for draft (aka "temporary") role */
    public const STATUS_DRAFT = 1;

    /**
     * ID of the user rule.
     *
     * @var mixed
     */
    public $id;

    /**
     * Only used when the role's status, is Role::STATUS_DRAFT.
     * Original role ID the draft was created from, or -1 if it's a new role.
     * Will be null if role's status is Role::STATUS_DEFINED.
     *
     * @since 6.0
     *
     * @var int|null
     */
    public $originalId;

    /**
     * Identifier of the role.
     *
     * Legacy note: Maps to name in 4.x.
     *
     * @var string
     */
    public $identifier;

    /**
     * Status of the role (legacy: "version").
     *
     * @since 6.0
     *
     * @var int One of Role::STATUS_DEFINED|Role::STATUS_DRAFT
     */
    public $status;

    /**
     * Name of the role.
     *
     * @since 5.0
     *
     * @var string[]
     */
    public $name;

    /**
     * Name of the role.
     *
     * @since 5.0
     *
     * @var string[]
     */
    public $description = [];

    /**
     * Policies associated with the role.
     *
     * @var \Ibexa\Contracts\Core\Persistence\User\Policy[]
     */
    public $policies = [];
}

class_alias(Role::class, 'eZ\Publish\SPI\Persistence\User\Role');
