<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;

/**
 * This class is used to create a new user in the repository.
 */
abstract class UserCreateStruct extends ContentCreateStruct
{
    /**
     * User login.
     *
     * Required.
     *
     * @var string
     */
    public $login;

    /**
     * User E-Mail address.
     *
     * Required.
     *
     * @var string
     */
    public $email;

    /**
     * The plain password.
     *
     * Required.
     *
     * @var string
     */
    public $password;

    /**
     * Indicates if the user is enabled after creation.
     *
     * @var bool
     */
    public $enabled = true;
}

class_alias(UserCreateStruct::class, 'eZ\Publish\API\Repository\Values\User\UserCreateStruct');
