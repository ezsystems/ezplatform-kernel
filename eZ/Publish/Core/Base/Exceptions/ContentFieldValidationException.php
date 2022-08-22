<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Base\Exceptions;

use eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException as APIContentFieldValidationException;
use eZ\Publish\Core\Base\Translatable;
use eZ\Publish\Core\Base\TranslatableBase;

/**
 * This Exception is thrown on create or update content one or more given fields are not valid.
 */
class ContentFieldValidationException extends APIContentFieldValidationException implements Translatable
{
    use TranslatableBase;

    private const MAX_MESSAGES_NUMBER = 32;

    /**
     * Contains an array of field ValidationError objects indexed with FieldDefinition id and language code.
     *
     * Example:
     * <code>
     *  $fieldErrors = $exception->getFieldErrors();
     *  $fieldErrors["43"]["eng-GB"]->getTranslatableMessage();
     * </code>
     *
     * @var array<array-key, array<string, \eZ\Publish\Core\FieldType\ValidationError>>
     */
    protected $errors;

    /** @var string|null */
    protected $contentName;

    /**
     * Generates: Content fields did not validate.
     *
     * Also sets the given $fieldErrors to the internal property, retrievable by getFieldErrors()
     *
     * @param array<array-key, array<string, \eZ\Publish\Core\FieldType\ValidationError>> $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        $this->setMessageTemplate('Content fields did not validate');
        parent::__construct($this->getBaseTranslation());
    }

    /**
     * Generates: Content fields did not validate exception with additional information on affected fields.
     *
     * @param array<array-key, array<string, \eZ\Publish\Core\FieldType\ValidationError>> $errors
     */
    public static function createNewWithMultiline(array $errors, ?string $contentName = null): self
    {
        $exception = new self($errors);
        $exception->contentName = $contentName;

        $exception->setMessageTemplate('Content "%contentName%" fields did not validate: %errors%');
        $exception->setParameters([
            '%errors%' => $exception->generateValidationErrorsMessages(),
            '%contentName%' => $exception->contentName !== null ? $exception->contentName : '',
        ]);
        $exception->message = $exception->getBaseTranslation();

        return $exception;
    }

    /**
     * Returns an array of field validation error messages.
     *
     * @return array<array-key, array<string, \eZ\Publish\Core\FieldType\ValidationError>>
     */
    public function getFieldErrors()
    {
        return $this->errors;
    }

    private function generateValidationErrorsMessages(): string
    {
        $validationErrors = $this->collectValidationErrors();
        $maxMessagesNumber = self::MAX_MESSAGES_NUMBER;

        if (count($validationErrors) > $maxMessagesNumber) {
            array_splice($validationErrors, $maxMessagesNumber);
            $validationErrors[] = sprintf('Limit: %d of validation errors has been exceeded.', $maxMessagesNumber);
        }

        return "\n- " . implode("\n- ", $validationErrors);
    }

    /**
     * @return array<\eZ\Publish\Core\FieldType\ValidationError>
     */
    private function collectValidationErrors(): array
    {
        $messages = [];
        foreach ($this->getFieldErrors() as $validationErrors) {
            foreach ($validationErrors as $validationError) {
                if (is_array($validationError)) {
                    foreach ($validationError as $item) {
                        $messages[] = $item->getTranslatableMessage();
                    }
                } else {
                    $messages[] = $validationError->getTranslatableMessage();
                }
            }
        }

        return $messages;
    }
}
