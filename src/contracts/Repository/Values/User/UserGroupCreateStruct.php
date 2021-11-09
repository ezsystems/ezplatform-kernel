<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;

/**
 * This class is used to create a new user group in the repository.
 */
abstract class UserGroupCreateStruct extends ContentCreateStruct
{
}

class_alias(UserGroupCreateStruct::class, 'eZ\Publish\API\Repository\Values\User\UserGroupCreateStruct');
