<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\PHPUnitConstraint;

/**
 * PHPUnit constraint checking that the given ValidationError message occurs in asserted ContentFieldValidationException.
 *
 * @see \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
 * @see \Ibexa\Contracts\Core\FieldType\ValidationError
 */
class ValidationErrorOccurs extends AllValidationErrorsOccur
{
    /** @var string */
    private $expectedValidationErrorMessage;

    /**
     * @param string $expectedValidationErrorMessage
     */
    public function __construct(string $expectedValidationErrorMessage)
    {
        $this->expectedValidationErrorMessage = $expectedValidationErrorMessage;

        parent::__construct([$expectedValidationErrorMessage]);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString(): string
    {
        return "contain the message '{$this->expectedValidationErrorMessage}'";
    }
}

class_alias(ValidationErrorOccurs::class, 'eZ\Publish\API\Repository\Tests\PHPUnitConstraint\ValidationErrorOccurs');
