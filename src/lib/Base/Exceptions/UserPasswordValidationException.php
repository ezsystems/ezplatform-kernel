<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Base\Exceptions;

use Exception;
use Ibexa\Core\FieldType\ValidationError;

class UserPasswordValidationException extends InvalidArgumentException
{
    /**
     * Generates: "Argument '{$argumentName}' is invalid: Password doesn't match the following rules: {X}, {Y}, {Z}".
     *
     * @param string $argumentName
     * @param array $errors
     * @param \Exception|null $previous
     */
    public function __construct(string $argumentName, array $errors, Exception $previous = null)
    {
        $rules = array_map(static function (ValidationError $error) {
            return (string) $error->getTranslatableMessage();
        }, $errors);

        parent::__construct($argumentName, 'The password does not match the following rules: ' . implode(', ', $rules), $previous);
    }
}

class_alias(UserPasswordValidationException::class, 'eZ\Publish\Core\Base\Exceptions\UserPasswordValidationException');
