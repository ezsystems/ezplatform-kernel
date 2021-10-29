<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class represents a policy value.
 *
 * @property-read mixed $id internal id of the policy
 * @property-read mixed $roleId the role id this policy belongs to
 * @property-read string $module Name of module, associated with the Policy
 * @property-read string $function  Name of the module function Or all functions with '*'
 * @property-read \Ibexa\Contracts\Core\Repository\Values\User\Limitation[] $limitations an array of \Ibexa\Contracts\Core\Repository\Values\User\Limitation
 */
abstract class Policy extends ValueObject
{
    /**
     * ID of the policy.
     *
     * @var int
     */
    protected $id;

    /**
     * the ID of the role this policy belongs to.
     *
     * @var int
     */
    protected $roleId;

    /**
     * Name of module, associated with the Policy.
     *
     * Eg: content
     *
     * @var string
     */
    protected $module;

    /**
     * Name of the module function Or all functions with '*'.
     *
     * Eg: read
     *
     * @var string
     */
    protected $function;

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Limitation[]
     */
    abstract public function getLimitations(): iterable;
}

class_alias(Policy::class, 'eZ\Publish\API\Repository\Values\User\Policy');
