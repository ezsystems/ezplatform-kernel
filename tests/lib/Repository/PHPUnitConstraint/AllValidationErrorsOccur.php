<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\PHPUnitConstraint;

use Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException;
use Ibexa\Contracts\Core\Repository\Translatable;
use PHPUnit\Framework\Constraint\Constraint as AbstractPHPUnitConstraint;

/**
 * PHPUnit constraint checking that all the given validation error messages occur in the asserted
 * ContentFieldValidationException.
 *
 * @see \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
 * @see \Ibexa\Contracts\Core\FieldType\ValidationError
 */
class AllValidationErrorsOccur extends AbstractPHPUnitConstraint
{
    /** @var string[] */
    private $expectedValidationErrorMessages;

    /**
     * @var string[]
     */
    private $missingValidationErrorMessages = [];

    /**
     * @param string[] $expectedValidationErrorMessages
     */
    public function __construct(array $expectedValidationErrorMessages)
    {
        $this->expectedValidationErrorMessages = $expectedValidationErrorMessages;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException $other
     *
     * @return bool
     */
    protected function matches($other): bool
    {
        $allFieldErrors = $this->extractAllFieldErrorMessages($other);

        $this->missingValidationErrorMessages = array_diff(
            $this->expectedValidationErrorMessages,
            $allFieldErrors
        );

        return empty($this->missingValidationErrorMessages);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException $exception
     *
     * @return string[]
     */
    private function extractAllFieldErrorMessages(ContentFieldValidationException $exception): array
    {
        $allFieldErrors = [];
        foreach ($exception->getFieldErrors() as $errors) {
            foreach ($errors as $fieldErrors) {
                $allFieldErrors = array_merge(
                    $allFieldErrors,
                    array_map(
                        static function (Translatable $translatableFieldError) {
                            return $translatableFieldError->getTranslatableMessage()->message;
                        },
                        $fieldErrors
                    )
                );
            }
        }

        return $allFieldErrors;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException $other
     *
     * @return string
     */
    protected function failureDescription($other): string
    {
        return sprintf(
            "the following Content Field validation error messages:\n%s\n%s",
            var_export($this->extractAllFieldErrorMessages($other), true),
            $this->toString()
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString(): string
    {
        $messages = implode(', ', $this->missingValidationErrorMessages);

        return "contain the messages: '{$messages}'";
    }
}

class_alias(AllValidationErrorsOccur::class, 'eZ\Publish\API\Repository\Tests\PHPUnitConstraint\AllValidationErrorsOccur');
