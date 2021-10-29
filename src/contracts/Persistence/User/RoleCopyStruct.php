<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Persistence\User;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class RoleCopyStruct extends ValueObject
{
    /**
     * ID of user role being cloned.
     *
     * @var int
     */
    public $clonedId;

    /**
     * Identifier of new role.
     *
     * @var string
     */
    public $newIdentifier;

    /**
     * Status of new role.
     *
     * @var string
     */
    public $status;

    /**
     * Contains an array of role policies.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct[]
     */
    public $policies = [];
}

class_alias(RoleCopyStruct::class, 'eZ\Publish\SPI\Persistence\User\RoleCopyStruct');
