<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\User;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;

/**
 * @internal
 */
interface PasswordValidatorInterface
{
    /**
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validatePassword(string $password, FieldDefinition $userFieldDefinition): array;
}

class_alias(PasswordValidatorInterface::class, 'eZ\Publish\Core\Repository\User\PasswordValidatorInterface');
