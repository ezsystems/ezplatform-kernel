<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\User;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class RoleAssignment extends ValueObject
{
    /**
     * The role assignment id.
     *
     * @var mixed
     */
    public $id;

    /**
     * The Role connected to this assignment.
     *
     * @var mixed
     */
    public $roleId;

    /**
     * The user or user group id.
     *
     * @var mixed
     */
    public $contentId;

    /**
     * One of 'Subtree' or 'Section'.
     *
     * @var string|null
     */
    public $limitationIdentifier;

    /**
     * The subtree paths or section ids.
     *
     * @var mixed[]|null
     */
    public $values;
}

class_alias(RoleAssignment::class, 'eZ\Publish\SPI\Persistence\User\RoleAssignment');
