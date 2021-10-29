<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class is used to update a role.
 */
class RoleUpdateStruct extends ValueObject
{
    /**
     * Readable string identifier of a role.
     *
     * @var string
     */
    public $identifier;
}

class_alias(RoleUpdateStruct::class, 'eZ\Publish\API\Repository\Values\User\RoleUpdateStruct');
