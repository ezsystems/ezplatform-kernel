<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class is used to update a user group in the repository.
 */
class UserGroupUpdateStruct extends ValueObject
{
    /**
     * The update structure for the profile content.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct
     */
    public $contentUpdateStruct = null;

    /**
     * The update structure for the profile meta data.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentMetadataUpdateStruct
     */
    public $contentMetadataUpdateStruct = null;
}

class_alias(UserGroupUpdateStruct::class, 'eZ\Publish\API\Repository\Values\User\UserGroupUpdateStruct');
