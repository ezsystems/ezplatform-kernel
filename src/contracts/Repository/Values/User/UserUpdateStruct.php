<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class is used to update a user in the repository.
 */
class UserUpdateStruct extends ValueObject
{
    /**
     * If set the email address is updated with this value.
     *
     * @var string|null
     */
    public $email;

    /**
     * If set the password is updated with this plain password.
     *
     * @var string|null
     */
    public $password;

    /**
     * Flag to signal if user is enabled or not
     * If set the enabled status is changed to this value.
     *
     * @var bool|null
     */
    public $enabled;

    /**
     * Max number of time user is allowed to login
     * If set the maximal number of logins is changed to this value.
     *
     * @var int|null
     */
    public $maxLogin;

    /**
     * The update structure for the profile content.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct
     */
    public $contentUpdateStruct = null;

    /**
     * The update structure  for the profile meta data.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentMetadataUpdateStruct
     */
    public $contentMetadataUpdateStruct = null;
}

class_alias(UserUpdateStruct::class, 'eZ\Publish\API\Repository\Values\User\UserUpdateStruct');
