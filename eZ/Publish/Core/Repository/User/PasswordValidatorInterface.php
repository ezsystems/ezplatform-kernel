<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository\User;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\User\PasswordInfo;
use eZ\Publish\API\Repository\Values\User\User;

/**
 * @internal
 */
interface PasswordValidatorInterface
{
    /**
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validatePassword(
        string $password,
        FieldDefinition $userFieldDefinition,
        ?User $user = null
    ): array;

    public function getPasswordInfo(User $user, FieldDefinition $fieldDefinition): PasswordInfo;
}
