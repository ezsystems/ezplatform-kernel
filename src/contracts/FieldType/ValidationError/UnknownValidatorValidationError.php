<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\FieldType\ValidationError;

final class UnknownValidatorValidationError extends AbstractValidationError
{
    public function __construct(string $validatorIdentifier, string $target)
    {
        parent::__construct(
            "Validator '%validator%' is unknown",
            [
                '%validator%' => $validatorIdentifier,
            ],
            $target
        );
    }
}

class_alias(UnknownValidatorValidationError::class, 'eZ\Publish\SPI\FieldType\ValidationError\UnknownValidatorValidationError');
