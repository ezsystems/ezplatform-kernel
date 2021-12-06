<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class represents a role.
 *
 * @property-read int $id the internal id of the role
 * @property-read string $identifier the identifier of the role
 * @property-read \Ibexa\Contracts\Core\Repository\Values\User\Policy[] $policies an array of the policies {@link \Ibexa\Contracts\Core\Repository\Values\User\Policy} of the role.
 */
abstract class Role extends ValueObject
{
    /** @var int Status constant for defined (aka "published") role */
    public const STATUS_DEFINED = 0;

    /** @var int Status constant for draft (aka "temporary") role */
    public const STATUS_DRAFT = 1;

    /**
     * ID of the user role.
     *
     * @var int
     */
    protected $id;

    /**
     * Readable string identifier of a role
     * in 4.x. this is mapped to the role name.
     *
     * @var string
     */
    protected $identifier;

    /**
     * The status of the role.
     *
     * @var int One of Role::STATUS_DEFINED|Role::STATUS_DRAFT
     */
    protected $status;

    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Returns the list of policies of this role.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Policy[]
     */
    abstract public function getPolicies(): iterable;
}

class_alias(Role::class, 'eZ\Publish\API\Repository\Values\User\Role');
