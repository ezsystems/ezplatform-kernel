<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Exception;

use Ibexa\Core\Base\Exceptions\NotFoundException;

/**
 * Exception thrown when a Role/RoleDraft to be loaded is not found.
 */
class RoleNotFound extends NotFoundException
{
    /**
     * Creates a new exception for $roleId in $status.
     *
     * @param mixed $roleId
     * @param mixed $status
     */
    public function __construct($roleId, $status)
    {
        parent::__construct(
            'Persistence User Role',
            sprintf('ID: %s, Status: %s', $roleId, $status)
        );
    }
}

class_alias(RoleNotFound::class, 'eZ\Publish\Core\Persistence\Legacy\Exception\RoleNotFound');
