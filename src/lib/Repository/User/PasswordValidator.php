<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\User;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Validator\UserPasswordValidator;

/**
 * @internal
 */
final class PasswordValidator implements PasswordValidatorInterface
{
    /**
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validatePassword(string $password, FieldDefinition $userFieldDefinition): array
    {
        $configuration = $userFieldDefinition->getValidatorConfiguration();
        if (!isset($configuration['PasswordValueValidator'])) {
            return [];
        }

        return (new UserPasswordValidator($configuration['PasswordValueValidator']))->validate($password);
    }
}

class_alias(PasswordValidator::class, 'eZ\Publish\Core\Repository\User\PasswordValidator');
