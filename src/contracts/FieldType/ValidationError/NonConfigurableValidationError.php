<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\FieldType\ValidationError;

final class NonConfigurableValidationError extends AbstractValidationError
{
    public function __construct(string $fieldTypeValidatorIdentifier, string $target)
    {
        parent::__construct(
            "FieldType '%fieldType%' does not accept settings",
            [
                '%fieldType%' => $fieldTypeValidatorIdentifier,
            ],
            $target
        );
    }
}

class_alias(NonConfigurableValidationError::class, 'eZ\Publish\SPI\FieldType\ValidationError\NonConfigurableValidationError');
