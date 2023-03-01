<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Tests\PHPUnitConstraint;

use eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException;
use PHPUnit\Framework\Constraint\Constraint as AbstractPHPUnitConstraint;

/**
 * PHPUnit's constraint checking that all the given validation error messages occur in the asserted
 * ContentFieldValidationException.
 *
 * @see \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
 * @see \eZ\Publish\SPI\FieldType\ValidationError
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
     * @param \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException $other
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
     * @param \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException $exception
     *
     * @return string[]
     */
    private function extractAllFieldErrorMessages(ContentFieldValidationException $exception): array
    {
        return iterator_to_array($this->extractTranslatable($exception->getFieldErrors()));
    }

    private function extractTranslatable(array $fieldErrors): \Traversable
    {
        // structure: [<fieldId> => [<languageCode> => array<ValidationError>]]
        foreach (array_merge(...$fieldErrors) as $errorsPerTranslation) {
            foreach ($errorsPerTranslation as $fieldError) {
                yield (string)$fieldError->getTranslatableMessage();
            }
        }
    }

    /**
     * @param \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException $other
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
